<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\StockMovementTypeEnum;
use App\Models\ProductStock;
use App\Models\ProductStockMovement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class RecordStockMovement
{
    use AsAction;

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): ProductStockMovement
    {
        Validator::make($data, [
            'type' => ['required', Rule::enum(StockMovementTypeEnum::class)],
        ])->validate();

        $type = $data['type'] instanceof StockMovementTypeEnum
            ? $data['type']
            : StockMovementTypeEnum::from((int) $data['type']);
        $isAdjustment = $type === StockMovementTypeEnum::ADJUSTMENT;

        Validator::make($data, [
            'user_id' => ['required', 'exists:users,id'],
            'branch_id' => ['required', 'exists:branches,id'],
            'product_item_id' => ['required', 'exists:product_items,id'],
            'type' => ['required', Rule::enum(StockMovementTypeEnum::class)],
            'quantity' => ['required', 'integer', $isAdjustment ? 'not_in:0' : 'min:1'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'transaction_date' => ['nullable', 'date'],
            'student_id' => ['nullable', 'exists:students,id'],
            'reference' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'destination_branch_id' => [
                'nullable',
                'exists:branches,id',
                'different:branch_id',
                Rule::requiredIf($type === StockMovementTypeEnum::TRANSFER_OUT),
            ],
        ])->validate();

        return DB::transaction(function () use ($data, $type): ProductStockMovement {
            $signedQuantity = $this->calculateSignedQuantity($type, (int) $data['quantity']);
            $transactionDate = isset($data['transaction_date'])
                ? Carbon::parse((string) $data['transaction_date'])
                : now();

            if ($signedQuantity < 0) {
                $this->decrementStockWithLock(
                    $data['product_item_id'],
                    $data['branch_id'],
                    abs($signedQuantity),
                );
            }

            $movement = ProductStockMovement::create([
                'user_id' => $data['user_id'],
                'branch_id' => $data['branch_id'],
                'product_item_id' => $data['product_item_id'],
                'type' => $type,
                'quantity' => $signedQuantity,
                'purchase_price' => $data['purchase_price'],
                'sale_price' => $data['sale_price'],
                'transaction_date' => $transactionDate,
                'student_id' => $data['student_id'] ?? null,
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            if ($signedQuantity > 0) {
                $this->incrementStock($data['product_item_id'], $data['branch_id'], $signedQuantity);
            }

            if ($type === StockMovementTypeEnum::TRANSFER_OUT) {
                $this->createTransferInMovement($movement, $data);
            }

            return $movement;
        });
    }

    protected function calculateSignedQuantity(StockMovementTypeEnum $type, int $quantity): int
    {
        return match ($type) {
            StockMovementTypeEnum::STOCK_IN,
            StockMovementTypeEnum::TRANSFER_IN => abs($quantity),

            StockMovementTypeEnum::DISTRIBUTION,
            StockMovementTypeEnum::DIRECT_SALE,
            StockMovementTypeEnum::TRANSFER_OUT => -abs($quantity),

            StockMovementTypeEnum::ADJUSTMENT => $quantity,
        };
    }

    protected function incrementStock(string $productItemId, string $branchId, int $quantity): void
    {
        if ($quantity <= 0) {
            return;
        }

        $stock = ProductStock::query()->firstOrCreate(
            [
                'product_item_id' => $productItemId,
                'branch_id' => $branchId,
            ],
            ['quantity' => 0],
        );

        $stock->increment('quantity', $quantity);
    }

    protected function decrementStockWithLock(string $productItemId, string $branchId, int $requiredQuantity): void
    {
        $stock = ProductStock::query()
            ->where('product_item_id', $productItemId)
            ->where('branch_id', $branchId)
            ->lockForUpdate()
            ->first();

        if ($stock === null) {
            throw ValidationException::withMessages([
                'quantity' => "Stok tidak mencukupi. Stok saat ini: 0, dibutuhkan: {$requiredQuantity}.",
            ]);
        }

        $currentStock = $stock->quantity;

        if ($currentStock < $requiredQuantity) {
            throw ValidationException::withMessages([
                'quantity' => "Stok tidak mencukupi. Stok saat ini: {$currentStock}, dibutuhkan: {$requiredQuantity}.",
            ]);
        }

        $updatedRows = ProductStock::query()
            ->whereKey($stock->getKey())
            ->where('quantity', '>=', $requiredQuantity)
            ->decrement('quantity', $requiredQuantity);

        if ($updatedRows === 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Stok berubah saat diproses. Silakan ulangi transaksi.',
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createTransferInMovement(ProductStockMovement $sourceMovement, array $data): void
    {
        $transferIn = ProductStockMovement::create([
            'user_id' => $data['user_id'],
            'branch_id' => $data['destination_branch_id'],
            'product_item_id' => $data['product_item_id'],
            'type' => StockMovementTypeEnum::TRANSFER_IN,
            'quantity' => abs((int) $data['quantity']),
            'purchase_price' => $data['purchase_price'],
            'sale_price' => $data['sale_price'],
            'transaction_date' => $sourceMovement->transaction_date,
            'related_movement_id' => $sourceMovement->getKey(),
            'reference' => $data['reference'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $sourceMovement->update(['related_movement_id' => $transferIn->getKey()]);

        $this->incrementStock($data['product_item_id'], $data['destination_branch_id'], abs((int) $data['quantity']));
    }
}

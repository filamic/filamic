<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\StockMovements\Pages;

use App\Actions\RecordStockMovement;
use App\Filament\SupplyHub\Resources\StockMovements\StockMovementResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['user_id'] = auth()->id();
        $data['branch_id'] = Filament::getTenant()->getKey();

        try {
            return RecordStockMovement::run($data);
        } catch (ValidationException $exception) {
            throw ValidationException::withMessages($this->mapValidationErrorsToFormState($exception->errors()));
        }
    }

    /**
     * @param  array<string, array<int, string>>  $errors
     * @return array<string, array<int, string>>
     */
    protected function mapValidationErrorsToFormState(array $errors): array
    {
        $statePath = $this->form->getStatePath();

        if (blank($statePath)) {
            return $errors;
        }

        $mappedErrors = [];

        foreach ($errors as $key => $messages) {
            if (blank($key) || str_starts_with($key, "{$statePath}.")) {
                $mappedErrors[$key] = $messages;

                continue;
            }

            $mappedErrors["{$statePath}.{$key}"] = $messages;
        }

        return $mappedErrors;
    }
}

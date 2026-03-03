<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\StockMovements\Pages;

use App\Actions\RecordStockMovement;
use App\Filament\SupplyHub\Resources\StockMovements\StockMovementResource;
use Filament\Facades\Filament;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStockMovement extends CreateRecord
{
    protected static string $resource = StockMovementResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $data['user_id'] = auth()->id();
        $data['branch_id'] = Filament::getTenant()->getKey();

        return RecordStockMovement::run($data);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\StockMovements\Pages;

use App\Enums\StockMovementTypeEnum;
use App\Filament\SupplyHub\Resources\StockMovements\StockMovementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListStockMovements extends ListRecords
{
    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $types = StockMovementTypeEnum::cases();
        $tabs = [];

        foreach ($types as $type) {
            $tabs[$type->value] = Tab::make()
                ->label($type->getLabel())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', $type->value))
                ->icon($type->getIcon());
        }
        
        return $tabs;
    }
}

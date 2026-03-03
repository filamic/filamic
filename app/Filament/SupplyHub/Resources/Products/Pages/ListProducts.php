<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Products\Pages;

use App\Enums\LevelEnum;
use App\Filament\SupplyHub\Resources\Products\ProductResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $levels = LevelEnum::cases();

        $tabs = [];

        foreach ($levels as $level) {
            $tabs[] = Tab::make()
                ->label($level->getLabel())
                ->icon($level->getIcon())
                ->modifyQueryUsing(fn (Builder $query) => $query->where('level', $level));
        }

        return $tabs;
    }
}

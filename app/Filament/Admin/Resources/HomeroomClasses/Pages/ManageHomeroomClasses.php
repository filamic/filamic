<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\HomeroomClasses\Pages;

use App\Filament\Admin\Resources\HomeroomClasses\HomeroomClassResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ManageHomeroomClasses extends ListRecords
{
    protected static string $resource = HomeroomClassResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Cms\Resources\SchoolEvents\Pages;

use App\Filament\Cms\Resources\SchoolEvents\SchoolEventResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSchoolEvents extends ManageRecords
{
    protected static string $resource = SchoolEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Curricula\Pages;

use App\Filament\Admin\Resources\Curricula\CurriculumResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCurricula extends ManageRecords
{
    protected static string $resource = CurriculumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

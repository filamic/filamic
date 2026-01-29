<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\LearningGroups\Pages;

use App\Filament\Admin\Resources\LearningGroups\LearningGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLearningGroups extends ManageRecords
{
    protected static string $resource = LearningGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

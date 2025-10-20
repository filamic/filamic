<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachers\Pages;

use App\Filament\Admin\Resources\Teachers\RelationManagers\TeachingsRelationManager;
use App\Filament\Admin\Resources\Teachers\TeacherResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewTeacher extends ViewRecord
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
            ]),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            TeachingsRelationManager::class,
        ];
    }
}

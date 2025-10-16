<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Classrooms\Pages;

use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClassroom extends ViewRecord
{
    protected static string $resource = ClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
            ]),
        ];
    }
}

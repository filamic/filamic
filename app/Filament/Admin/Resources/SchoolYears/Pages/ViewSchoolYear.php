<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolYears\Pages;

use App\Filament\Admin\Resources\SchoolYears\SchoolYearResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSchoolYear extends ViewRecord
{
    protected static string $resource = SchoolYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
            ]),
        ];
    }
}

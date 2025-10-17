<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubjectCategories\Pages;

use App\Filament\Admin\Resources\SubjectCategories\SubjectCategoryResource;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSubjectCategory extends ViewRecord
{
    protected static string $resource = SubjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                EditAction::make(),
            ]),
        ];
    }
}

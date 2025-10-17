<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubjectCategories\Pages;

use App\Filament\Admin\Resources\SubjectCategories\SubjectCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditSubjectCategory extends EditRecord
{
    protected static string $resource = SubjectCategoryResource::class;
}

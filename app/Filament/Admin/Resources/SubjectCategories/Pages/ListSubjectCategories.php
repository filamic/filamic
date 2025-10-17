<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SubjectCategories\Pages;

use App\Filament\Admin\Resources\SubjectCategories\SubjectCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubjectCategories extends ListRecords
{
    protected static string $resource = SubjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getSubheading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'Subject categories you have access to are listed below.';
    }
}

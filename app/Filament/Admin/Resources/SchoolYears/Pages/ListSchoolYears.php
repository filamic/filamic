<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolYears\Pages;

use App\Filament\Admin\Resources\SchoolYears\SchoolYearResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSchoolYears extends ListRecords
{
    protected static string $resource = SchoolYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getSubheading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'Manage school academic years and semesters.';
    }
}

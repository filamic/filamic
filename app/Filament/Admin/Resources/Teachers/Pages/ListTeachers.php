<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachers\Pages;

use App\Filament\Admin\Resources\Teachers\TeacherResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getSubheading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'Teachers you have access to are listed below.';
    }
}

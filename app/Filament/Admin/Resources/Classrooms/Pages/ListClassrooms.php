<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Classrooms\Pages;

use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClassrooms extends ListRecords
{
    protected static string $resource = ClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getSubheading(): string | \Illuminate\Contracts\Support\Htmlable | null
    {
        return 'Classrooms you have access to are listed below.';
    }
}

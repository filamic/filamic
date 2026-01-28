<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Filament\Admin\Resources\Students\StudentResource;
use App\Models\Student;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'active' => Tab::make()
                ->modifyQueryUsing(callback: fn (Student $query) => $query->active())
                ->icon('tabler-rosette-discount-check'),
            'prospective' => Tab::make()
                ->modifyQueryUsing(fn (Student $query) => $query->prospective())
                ->icon('tabler-star'),
            'graduated' => Tab::make()
                ->modifyQueryUsing(fn (Student $query) => $query->graduated())
                ->icon('tabler-briefcase-2'),
            'moved' => Tab::make()
                ->modifyQueryUsing(fn (Student $query) => $query->moved())
                ->icon('tabler-outbound'),
            'dropped_out' => Tab::make()
                ->modifyQueryUsing(fn (Student $query) => $query->droppedOut())
                ->icon('tabler-arrow-bear-right'),
            'nonActive' => Tab::make()
                ->modifyQueryUsing(fn (Student $query) => $query->nonActive())
                ->icon('tabler-rosette-discount-check-off'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Students\Pages;

use App\Enums\StudentStatusEnum;
use App\Filament\Admin\Resources\Students\StudentResource;
use App\Models\Student;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

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
        return collect(StudentStatusEnum::cases())
            ->mapWithKeys(function (StudentStatusEnum $status) {
                
                return [
                    $status->name => Tab::make()
                        ->label(fn()=>str($status->name)->replace('_', ' ')->title())
                        ->modifyQueryUsing(fn (Builder|Student $query) => $query->{str($status->name)->camel()->toString()}())
                        ->icon($status->getIcon()),
                ];
                
            })
            ->toArray();
    }
}

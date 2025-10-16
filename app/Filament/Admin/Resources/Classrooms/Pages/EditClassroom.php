<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Classrooms\Pages;

use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use Filament\Resources\Pages\EditRecord;

class EditClassroom extends EditRecord
{
    protected static string $resource = ClassroomResource::class;
}

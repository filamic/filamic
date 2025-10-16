<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Classrooms\Pages;

use App\Filament\Admin\Resources\Classrooms\ClassroomResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClassroom extends CreateRecord
{
    protected static string $resource = ClassroomResource::class;
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Teachers\Pages;

use App\Filament\Admin\Resources\Teachers\TeacherResource;
use Filament\Resources\Pages\EditRecord;

class EditTeacher extends EditRecord
{
    protected static string $resource = TeacherResource::class;
}

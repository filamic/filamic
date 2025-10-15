<?php

declare(strict_types=1);

namespace App\Filament\Resources\Schools\Pages;

use App\Filament\Resources\Schools\SchoolResource;
use Filament\Resources\Pages\EditRecord;

class EditSchool extends EditRecord
{
    protected static string $resource = SchoolResource::class;
}

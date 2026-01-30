<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\Pages;

use App\Filament\Finance\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
}

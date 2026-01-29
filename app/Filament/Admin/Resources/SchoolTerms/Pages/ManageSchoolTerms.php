<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolTerms\Pages;

use App\Filament\Admin\Resources\SchoolTerms\SchoolTermResource;
use Filament\Resources\Pages\ManageRecords;

class ManageSchoolTerms extends ManageRecords
{
    protected static string $resource = SchoolTermResource::class;
}

<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\SchoolYears\Pages;

use App\Filament\Admin\Resources\SchoolYears\SchoolYearResource;
use App\Models\SchoolYear;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSchoolYear extends CreateRecord
{
    protected static string $resource = SchoolYearResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if (data_get($data, 'is_active')) {
            SchoolYear::deactivateOthers();
        }

        return static::getModel()::create($data);
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Cms\Resources\Galleries\Pages;

use App\Filament\Cms\Resources\Galleries\GalleryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGallery extends CreateRecord
{
    protected static string $resource = GalleryResource::class;
}

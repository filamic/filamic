<?php

declare(strict_types=1);

namespace App\Filament\Cms\Resources\Galleries;

use App\Filament\Cms\Resources\Galleries\Pages\CreateGallery;
use App\Filament\Cms\Resources\Galleries\Pages\EditGallery;
use App\Filament\Cms\Resources\Galleries\Pages\ListGalleries;
use App\Filament\Cms\Resources\Galleries\Schemas\GalleryForm;
use App\Filament\Cms\Resources\Galleries\Tables\GalleriesTable;
use App\Models\Gallery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPhoto;

    public static function form(Schema $schema): Schema
    {
        return GalleryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GalleriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGalleries::route('/'),
            'create' => CreateGallery::route('/create'),
            'edit' => EditGallery::route('/{record}/edit'),
        ];
    }
}

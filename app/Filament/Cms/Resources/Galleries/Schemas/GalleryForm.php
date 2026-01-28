<?php

declare(strict_types=1);

namespace App\Filament\Cms\Resources\Galleries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    Select::make('school_id')
                        ->relationship('school', 'name'),
                    Select::make('gallery_category_id')
                        ->relationship('category', 'name'),
                    DatePicker::make('event_date')
                        ->required()
                        ->columnSpanFull(),
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', str($state)->slug())),
                    TextInput::make('slug')
                        ->required()
                        ->disabled()
                        ->dehydrated()
                        ->helperText('This field is auto-generated and cannot be edited for SEO purposes.'),
                    FileUpload::make('images')
                        ->required()
                        ->image()
                        ->disk('public')
                        ->maxSize(2048)
                        ->directory('event-galleries')
                        ->panelLayout('grid')
                        ->multiple()
                        ->columnSpanFull()
                        ->formatStateUsing(fn ($state) => collect($state)->map(fn ($file) => "event-galleries/{$file}")->toArray())
                        ->dehydrateStateUsing(fn (array $state) => collect($state)->map(fn ($path) => basename($path))->toArray()),
                ]),
            ]);
    }
}

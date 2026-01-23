<?php

namespace App\Filament\Cms\Resources\SchoolEvents;

use App\Filament\Cms\Resources\SchoolEvents\Pages\ManageSchoolEvents;
use App\Models\SchoolEvent;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SchoolEventResource extends Resource
{
    protected static ?string $model = SchoolEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Calendar;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('school_id')
                    ->relationship('school', 'name')
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('location')
                    ->required(),
                TimePicker::make('start_at')
                    ->required(),
                TimePicker::make('ends_at')
                    ->after('starts_at')
                    ->required(),
                FileUpload::make('image')
                    ->image()
                    ->columnSpanFull(),
                Textarea::make('details')
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school.name')
                    ->label('School')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('location'),
                TextEntry::make('starts_at')
                    ->time(),
                TextEntry::make('ends_at')
                    ->time(),
                ImageEntry::make('image')
                    ->placeholder('-'),
                TextEntry::make('details')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('school.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('location')
                    ->searchable(),
                TextColumn::make('starts_at')
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSchoolEvents::route('/'),
        ];
    }
}

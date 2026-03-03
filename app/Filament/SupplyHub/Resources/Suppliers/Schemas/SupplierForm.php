<?php

declare(strict_types=1);

namespace App\Filament\SupplyHub\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading('Informasi Supplier')
                    ->icon('tabler-article')
                    ->columns(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Nama Supplier')
                            ->required()
                            ->columnSpanFull()
                            ->unique(ignoreRecord: true),
                        TextInput::make('contact_person')
                            ->label('Nama Penanggung Jawab'),
                        TextInput::make('phone')
                            ->label('WA')
                            ->tel(),
                    ]),
            ]);
    }
}

<?php

namespace App\Filament\Resources\Schools\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('address'),
                        TextEntry::make('npsn')->label('NPSN'),
                        TextEntry::make('nis_nss_nds')->label('NIS/NSS/NDS'),
                        TextEntry::make('telp'),
                        TextEntry::make('postal_code'),
                        TextEntry::make('village'),
                        TextEntry::make('subdistrict'),
                        TextEntry::make('city'),
                        TextEntry::make('province'),
                        TextEntry::make('website'),
                        TextEntry::make('email'),
                    ])
            ]);
    }
}

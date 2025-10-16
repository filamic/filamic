<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Schools\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class SchoolForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('School Information')
                    ->tabs([
                        Tabs\Tab::make('School Identity')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->columnSpanFull(),
                                TextInput::make('npsn')->label('NPSN'),
                                TextInput::make('nis_nss_nds')->label('NIS/NSS/NDS'),
                            ]),
                        Tabs\Tab::make('Address & Location')
                            ->icon(Heroicon::OutlinedMapPin)
                            ->schema([
                                TextInput::make('address'),
                                TextInput::make('village'),
                                TextInput::make('subdistrict'),
                                TextInput::make('city'),
                                TextInput::make('province'),
                                TextInput::make('postal_code'),
                            ]),
                        Tabs\Tab::make('Contact & Communication')
                            ->icon(Heroicon::OutlinedChatBubbleBottomCenterText)
                            ->schema([
                                TextInput::make('website')
                                    ->url()
                                    ->columnSpanFull(),
                                TextInput::make('telp')->tel(),
                                TextInput::make('email')->label('Email address')->email(),
                            ]),
                    ]),
            ]);
    }
}

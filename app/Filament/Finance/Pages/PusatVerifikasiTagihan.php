<?php

namespace App\Filament\Finance\Pages;

use Filament\Pages\Page;
use BackedEnum;

class PusatVerifikasiTagihan extends Page
{
    protected string $view = 'filament.finance.pages.pusat-verifikasi-tagihan';

    protected static string | BackedEnum | null $navigationIcon = 'tabler-clipboard-check';
}

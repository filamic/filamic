<?php

declare(strict_types=1);

namespace App\Filament\Finance\Pages;

use BackedEnum;
use Filament\Pages\Page;

class PusatVerifikasiTagihan extends Page
{
    protected string $view = 'filament.finance.pages.pusat-verifikasi-tagihan';

    protected static string | BackedEnum | null $navigationIcon = 'tabler-clipboard-check';

    protected static ?int $navigationSort = 2;
}

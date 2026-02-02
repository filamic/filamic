<?php

namespace App\Filament\Finance\Pages;

use Filament\Pages\Page;
use BackedEnum;

class Laporan extends Page
{
    protected string $view = 'filament.finance.pages.laporan';

    protected static string | BackedEnum | null $navigationIcon = 'tabler-report-analytics';
}

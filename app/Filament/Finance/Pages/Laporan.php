<?php

declare(strict_types=1);

namespace App\Filament\Finance\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Laporan extends Page
{
    protected string $view = 'filament.finance.pages.laporan';

    protected static string | BackedEnum | null $navigationIcon = 'tabler-report-analytics';

    protected static ?int $navigationSort = 3;
}

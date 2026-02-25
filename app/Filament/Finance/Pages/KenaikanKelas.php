<?php

declare(strict_types=1);

namespace App\Filament\Finance\Pages;

use BackedEnum;
use Filament\Pages\Page;

class KenaikanKelas extends Page
{
    protected string $view = 'filament.finance.pages.kenaikan-kelas';

    protected static string | BackedEnum | null $navigationIcon = 'tabler-arrow-up-right-circle';

    protected static ?int $navigationSort = 4;
}

<?php

namespace App\Filament\Finance\Pages;

use Filament\Pages\Page;
use BackedEnum;

class KenaikanKelas extends Page
{
    protected string $view = 'filament.finance.pages.kenaikan-kelas';

    protected static string | BackedEnum | null $navigationIcon = 'tabler-arrow-up-right-circle';
}

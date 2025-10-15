<?php

declare(strict_types=1);

namespace App\Providers;

use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Illuminate\Support\ServiceProvider;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Entry;
use Filament\Schemas\Components\Section;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void {
        

        Section::configureUsing(fn (Section $section) => $section
            ->columnSpanFull()
            ->columns(2)
        );
        
        Tabs::configureUsing(fn (Tabs $tabs) => $tabs
            ->columnSpanFull()
            ->columns(2)
        );

        TextInput::configureUsing(function (TextInput $textInput) {
            return $textInput
                ->maxLength(2 ** 8 - 1); // 255 (MySQL VARCHAR(255), Laravel 'string')
        });

        Textarea::configureUsing(fn (Textarea $textArea) => $textArea
            ->maxLength(2 ** 16 - 1) // 65,535 (MySQL TEXT, Laravel 'text')
            ->rows(4));

        ViewAction::configureUsing(function (ViewAction $action) {
            $action
                ->iconButton()
                ->icon('heroicon-o-chevron-right');
        }, isImportant: true);

        Entry::configureUsing(fn (Entry $field) => $field
            
            ->placeholder('None')
        );
    }
}

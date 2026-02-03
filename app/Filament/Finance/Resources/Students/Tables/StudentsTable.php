<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['currentPaymentAccount']);
            })
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable(),

                TextColumn::make('currentPaymentAccount')
                    ->label('Payment Accounts')
                    ->markdown()
                    ->formatStateUsing(function ($record) {
                        $account = $record->currentPaymentAccount;

                        if (! $account) {
                            return '-';
                        }

                        return "**SPP:** {$account->monthly_fee_virtual_account}  \n**Buku:** {$account->book_fee_virtual_account}";
                    }),
            ])
            ->filters([

            ])
            ->recordActions([
                EditAction::make()
                    ->iconButton()
                    ->size(Size::ExtraSmall)
                    ->tooltip('Ubah'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                ]),
            ]);
    }
}

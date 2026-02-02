<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\Schemas;

use App\Enums\GenderEnum;
use App\Enums\StudentStatusEnum;
use App\Models\Classroom;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\Student;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\RawJs;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Detail Siswa') // Label diubah dari Student's Detail
                            ->schema([
                                Section::make([
                                    TextInput::make('name')
                                        ->label('Nama Lengkap')
                                        ->required()
                                        ->placeholder('Contoh: John Doe'),
                                    ToggleButtons::make('gender')
                                        ->label('Jenis Kelamin')
                                        ->options(GenderEnum::class)
                                        ->required()
                                        ->inline(),
                                    // ToggleButtons::make('status')
                                    //     ->label('Status Siswa')
                                    //     ->options(StudentStatusEnum::class)
                                    //     ->required()
                                    //     ->inline()
                                    //     ->columnSpanFull(),
                                    TextInput::make('previous_education')
                                        ->label('Pendidikan Sebelumnya')
                                        ->placeholder('Contoh: SDS Kasih Sayang'),
                                    TextInput::make('joined_at_class')
                                        ->label('Masuk di Kelas')
                                        ->placeholder('Contoh: VII (Joshua 1)'),
                                    Textarea::make('notes')
                                        ->label('Catatan Tambahan')
                                        ->columnSpanFull(),
                                ]),
                            ])
                            ->icon('tabler-list-details'),

                        Tab::make('Detail Pembayaran') // Label diubah dari Payment Detail
                            ->schema([
                                Section::make()
                                    ->columnSpanFull()
                                    ->description(
                                        str('Klik **Tambah Jenjang Lanjutan** jika siswa memiliki rencana pendaftaran untuk jenjang berikutnya (misal: saat ini TK, namun sudah daftar SD/SMP). Setiap unit wajib memiliki konfigurasi biaya dan VA masing-masing.')
                                            ->markdown()
                                            ->toHtmlString()
                                    )
                                    ->icon('tabler-info-circle')
                                    ->iconColor('info'),
                                Repeater::make('paymentAccounts')
                                    ->label('Akun Pembayaran Unit')
                                    ->addActionLabel('Tambah Jenjang Lanjutan')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->required()
                                    ->relationship('paymentAccounts')
                                    ->columns(2)
                                    ->minItems(1)
                                    ->schema([
                                        Select::make('school_id')
                                            ->label('Unit Sekolah')
                                            ->relationship('school', 'name')
                                            ->required()
                                            ->distinct()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                            ->columnSpanFull(),
                                        TextInput::make('monthly_fee_amount')
                                            ->label('Nominal SPP Bulanan')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->placeholder('0')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->minValue(0)
                                            ->required(),
                                        TextInput::make('book_fee_amount')
                                            ->label('Nominal Biaya Buku')
                                            ->numeric()
                                            ->prefix('Rp')
                                            ->placeholder('0')
                                            ->mask(RawJs::make('$money($input)'))
                                            ->stripCharacters(',')
                                            ->minValue(0)
                                            ->default(0)
                                            ->required(),
                                        TextInput::make('monthly_fee_virtual_account')
                                            ->label('VA SPP Bulanan')
                                            ->placeholder('Contoh: 103023001')
                                            ->prefixIcon('tabler-credit-card')
                                            ->numeric()
                                            ->minLength(5)
                                            ->maxLength(20)
                                            ->unique(ignoreRecord: true)
                                            ->different('book_fee_virtual_account'),
                                        TextInput::make('book_fee_virtual_account')
                                            ->label('VA Biaya Buku')
                                            ->placeholder('Contoh: 103023001')
                                            ->prefixIcon('tabler-book')
                                            ->numeric()
                                            ->minLength(5)
                                            ->maxLength(20)
                                            ->unique(ignoreRecord: true)
                                            ->different('monthly_fee_virtual_account'),
                                    ]),
                            ])
                            ->icon('tabler-wallet'),

                        Tab::make('Data Kelas') // Label diubah dari Classroom
                            ->schema([
                                Section::make()
                                    ->columnSpanFull()
                                    ->description(
                                        str('**Opsional** — Silakan lengkapi bagian ini jika siswa sudah memiliki penempatan kelas untuk tahun ajaran terkait.')
                                            ->markdown()
                                            ->toHtmlString()
                                    )
                                    ->icon('tabler-info-circle')
                                    ->iconColor('info'),
                                Repeater::make('enrollments')
                                    ->label('Riwayat Pendaftaran Kelas')
                                    ->maxItems(1)
                                    ->defaultItems(0)
                                    ->addActionLabel('Tambah Data Kelas')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->deletable(fn (?Student $record) => blank($record) ? true : $record->enrollments()->doesntExist())
                                    ->relationship('enrollments')
                                    ->columns(2)
                                    ->schema([
                                        Select::make('school_year_id')
                                            ->label('Tahun Ajaran')
                                            ->relationship('schoolYear', 'name')
                                            ->default(fn () => SchoolYear::getActive()?->getKey())
                                            ->required()
                                            ->hint(fn () => ($active = SchoolYear::getActive()) ? "Tahun ajaran aktif: {$active->name}" : 'Tahun ajaran belum aktif!'),
                                        ToggleButtons::make('school_term_id')
                                            ->label('Semester')
                                            ->options(fn () => SchoolTerm::all()->pluck('name.name', 'id'))
                                            ->default(fn () => SchoolTerm::getActive()?->getKey())
                                            ->inline()
                                            ->required()
                                            ->hint(fn () => ($active = SchoolTerm::getActive()) ? "Semester aktif: {$active->name->getLabel()}" : 'Semester belum aktif!'),
                                        Select::make('classroom_id')
                                            ->label('Pilih Kelas')
                                            ->options(fn () => Classroom::with('school')
                                                ->get()
                                                ->groupBy('school.name')
                                                ->map(fn ($classroom) => $classroom->pluck('name', 'id'))
                                            )
                                            ->preload()
                                            ->optionsLimit(20)
                                            ->searchable()
                                            ->columnSpanFull()
                                            ->required(),
                                    ]),
                            ])
                            ->icon('tabler-door'),
                    ])
                    ->contained(false),
            ]);
    }
}

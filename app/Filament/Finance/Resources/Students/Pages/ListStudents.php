<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\Students\Pages;

use App\Filament\Finance\Resources\Students\StudentResource;
use App\Models\Student;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'Aktif' => Tab::make()
                ->modifyQueryUsing(fn (Builder|Student $query) => $query->active())
                ->icon('tabler-user-check'),
            'Tidak Aktif' => Tab::make()
                ->modifyQueryUsing(fn (Builder|Student $query) => $query->inActive())
                ->icon('tabler-user-x'),
            // 'Mutasi Internal' => Tab::make()
            //     ->modifyQueryUsing(fn (Student $query) => $query->inActive())
            //     ->icon('tabler-user-down'),
            // 'Calon Siswa' => Tab::make()
            //     ->modifyQueryUsing(fn (Student $query) => $query->inActive())
            //     ->icon('tabler-user-star'),
        ];
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getTabsContentComponent(), // This method returns a component to display the tabs above a table
                $this->setTabInfo(),
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE),
                EmbeddedTable::make(), // This is the component that renders the table that is defined in this resource
                RenderHook::make(PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER),
            ]);
    }

    private function setTabInfo(): ?Section
    {
        // TODO: jadikan ini enum aja StudentStatus
        $content = [
            'Aktif' => [
                'title' => 'Daftar Siswa Aktif',
                'desc' => 'Menampilkan seluruh siswa yang terdaftar pada tahun ajaran berjalan.',
                'color' => 'success',
            ],
            'Tidak Aktif' => [
                'title' => 'Arsip Siswa',
                'desc' => 'Menampilkan siswa yang sudah lulus, pindah keluar, atau mengundurkan diri.',
                'color' => 'danger',
            ],
            'Mutasi Internal' => [
                'title' => 'Perpindahan Cabang',
                'desc' => 'Menampilkan siswa yang masuk melalui jalur mutasi antar unit sekolah dalam yayasan.',
                'color' => 'info',
            ],
            'Calon Siswa' => [
                'title' => 'Penerimaan Baru',
                'desc' => 'Menampilkan calon siswa yang telah mendaftar untuk tahun ajaran mendatang.',
                'color' => 'warning',
            ],
        ];

        $detail = $content[$this->activeTab] ?? null;

        if (! $detail) {
            return null;
        }

        return Section::make()
            ->columnSpanFull()
            ->description(
                str("**{$detail['title']}** — {$detail['desc']}")
                    ->markdown()
                    ->toHtmlString()
            )
            ->icon($this->getTabs()[$this->activeTab]->getIcon())
            ->iconColor($detail['color']);
    }
}

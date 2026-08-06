<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class LaporanKeuanganExport implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected $data;
    protected $periodeStart;
    protected $periodeEnd;
    protected $userName;

    public function __construct(array $data, string $periodeStart, string $periodeEnd, string $userName)
    {
        $this->data = $data;
        $this->periodeStart = $periodeStart;
        $this->periodeEnd = $periodeEnd;
        $this->userName = $userName;
    }

    public function title(): string
    {
        return 'Laporan Keuangan';
    }

    public function array(): array
    {
        $rows = [];

        // === HEADER LAPORAN ===
        $rows[] = ['LAPORAN KEUANGAN KEBUN - BAKOELKEMBANG V3'];
        $rows[] = ['Premium Orchid Nursery & Botanical Fintech System'];
        $rows[] = [''];
        $rows[] = ['Periode', $this->periodeStart . ' s/d ' . $this->periodeEnd];
        $rows[] = ['Dicetak Oleh', $this->userName];
        $rows[] = ['Tanggal Cetak', now()->translatedFormat('d F Y H:i')];
        $rows[] = [''];

        // === RINGKASAN EKSEKUTIF ===
        $rows[] = ['RINGKASAN EKSEKUTIF KEUANGAN KEBUN'];
        $rows[] = [''];

        $totalOmzet = $this->data['total_omzet'] ?? 0;
        $totalModal = $this->data['total_modal'] ?? 0;
        $labaBersih = $totalOmzet - $totalModal;
        $nilaiAset  = $this->data['nilai_aset'] ?? 0;

        $rows[] = ['Metrik', 'Nilai (Rp)'];
        $rows[] = ['Total Omzet (Penjualan)', $totalOmzet];
        $rows[] = ['Total Modal (HPP / Pembelian)', $totalModal];
        $rows[] = ['Laba Bersih (Omzet - Modal)', $labaBersih];
        $rows[] = ['Nilai Aset Inventaris', $nilaiAset];
        $rows[] = ['Total Barang Masuk (frekuensi)', $this->data['total_masuk_count'] ?? 0];
        $rows[] = ['Total Barang Keluar (frekuensi)', $this->data['total_keluar_count'] ?? 0];
        $rows[] = [''];

        // === RINCIAN BARANG MASUK (PEMBELIAN / MODAL) ===
        $rows[] = ['RINCIAN BARANG MASUK (MODAL / PEMBELIAN)'];
        $rows[] = ['No', 'Nama Barang', 'Kode', 'Jumlah', 'Harga Satuan', 'Total Harga', 'Tanggal Masuk', 'Pemasok'];

        $no = 1;
        foreach (($this->data['barang_masuk'] ?? []) as $bm) {
            $rows[] = [
                $no++,
                $bm['nama_barang'] ?? '-',
                $bm['kode_barang'] ?? '-',
                $bm['jumlah'] ?? 0,
                $bm['harga_satuan'] ?? 0,
                $bm['total_harga'] ?? 0,
                $bm['tanggal_masuk'] ?? '-',
                $bm['pemasok'] ?? '-',
            ];
        }
        $rows[] = ['', '', '', '', 'TOTAL MODAL:', $totalModal, '', ''];
        $rows[] = [''];

        // === RINCIAN BARANG KELUAR (PENJUALAN / OMZET) ===
        $rows[] = ['RINCIAN BARANG KELUAR (PENJUALAN / OMZET)'];
        $rows[] = ['No', 'Nama Barang', 'Kode', 'Jumlah', 'Harga Jual', 'Total Penjualan', 'Tanggal Keluar', 'Penerima'];

        $no = 1;
        foreach (($this->data['barang_keluar'] ?? []) as $bk) {
            $rows[] = [
                $no++,
                $bk['nama_barang'] ?? '-',
                $bk['kode_barang'] ?? '-',
                $bk['jumlah'] ?? 0,
                $bk['harga_jual'] ?? 0,
                $bk['total_harga_jual'] ?? 0,
                $bk['tanggal_keluar'] ?? '-',
                $bk['penerima'] ?? '-',
            ];
        }
        $rows[] = ['', '', '', '', 'TOTAL OMZET:', $totalOmzet, '', ''];
        $rows[] = [''];

        // === FOOTER ===
        $rows[] = [''];
        $rows[] = ['© ' . date('Y') . ' BAKOELKEMBANG Premium Orchid & Inventory System'];
        $rows[] = ['Laporan ini dihasilkan secara otomatis oleh sistem Botanical Fintech V3.'];

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,
            'C' => 18,
            'D' => 12,
            'E' => 18,
            'F' => 20,
            'G' => 18,
            'H' => 22,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '0B4F35']],
            ],
            2 => [
                'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '8FA882']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge header cells
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');

                // Bold section headers
                $lastRow = $sheet->getHighestRow();
                for ($i = 1; $i <= $lastRow; $i++) {
                    $cellValue = $sheet->getCell('A' . $i)->getValue();
                    if (in_array($cellValue, [
                        'RINGKASAN EKSEKUTIF KEUANGAN KEBUN',
                        'RINCIAN BARANG MASUK (MODAL / PEMBELIAN)',
                        'RINCIAN BARANG KELUAR (PENJUALAN / OMZET)',
                    ])) {
                        $sheet->mergeCells('A' . $i . ':H' . $i);
                        $sheet->getStyle('A' . $i)->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '0B4F35']],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E8F5E9'],
                            ],
                        ]);
                    }

                    // Style table headers (No, Nama Barang, etc.)
                    if ($cellValue === 'No') {
                        $sheet->getStyle('A' . $i . ':H' . $i)->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '0B4F35'],
                            ],
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                            ],
                        ]);
                    }

                    // Style Metrik header
                    if ($cellValue === 'Metrik') {
                        $sheet->getStyle('A' . $i . ':B' . $i)->applyFromArray([
                            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '0B4F35'],
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}

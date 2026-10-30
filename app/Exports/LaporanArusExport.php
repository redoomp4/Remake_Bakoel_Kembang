<?php

namespace App\Exports;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanArusExport implements FromCollection, WithHeadings
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $start = $this->request->filled('start_date') ? Carbon::parse($this->request->start_date)->startOfDay() : null;
        $end = $this->request->filled('end_date') ? Carbon::parse($this->request->end_date)->endOfDay() : null;
        $kategori = $this->request->filled('kategori') ? (int) $this->request->kategori : null;
        $lokasi = $this->request->filled('lokasi') ? (int) $this->request->lokasi : null;
        $search = $this->request->input('search', $this->request->input('nama_barang'));
        $userId = Auth::id() ?? optional($this->request->user())->id;

        // Barang Masuk
        $arusMasuk = BarangMasuk::with(['item.kategori', 'lokasi', 'pemasok'])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($start, fn ($q) => $q->where('tanggal_masuk', '>=', $start))
            ->when($end, fn ($q) => $q->where('tanggal_masuk', '<=', $end))
            ->when($lokasi, fn ($q) => $q->where('id_lokasi', $lokasi))
            ->get()
            ->filter(function ($masuk) use ($kategori, $search) {
                $pihak = is_object($masuk->pemasok) ? ($masuk->pemasok->nama_pemasok ?? '') : (string) ($masuk->pemasok ?? '');

                return (! $kategori || ($masuk->item && $masuk->item->id_kategori == $kategori))
                    && (! $search || str_contains(mb_strtolower($masuk->item->nama_barang ?? ''), mb_strtolower($search))
                        || str_contains(mb_strtolower($masuk->item->kode_barang ?? ''), mb_strtolower($search))
                        || str_contains(mb_strtolower($pihak), mb_strtolower($search)));
            })
            ->map(function ($masuk) {
                $pihak = is_object($masuk->pemasok) ? ($masuk->pemasok->nama_pemasok ?? '-') : (string) ($masuk->pemasok ?? '-');

                return [
                    'tanggal' => $masuk->tanggal_masuk ? Carbon::parse($masuk->tanggal_masuk)->format('d/m/Y H:i:s') : '-',
                    'raw_date' => $masuk->tanggal_masuk,
                    'jenis' => 'Masuk',
                    'kode_barang' => $masuk->item->kode_barang ?? '-',
                    'nama_barang' => $masuk->item->nama_barang ?? '-',
                    'harga_satuan' => (float) ($masuk->harga_satuan ?? 0),
                    'jumlah' => (int) $masuk->jumlah,
                    'lokasi' => $masuk->lokasi->nama_lokasi ?? '-',
                    'pihak' => $pihak ?: '-',
                ];
            });

        // Barang Keluar
        $arusKeluar = BarangKeluar::with(['item.kategori', 'lokasi'])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($start, fn ($q) => $q->where('tanggal_keluar', '>=', $start))
            ->when($end, fn ($q) => $q->where('tanggal_keluar', '<=', $end))
            ->when($lokasi, fn ($q) => $q->where('id_lokasi', $lokasi))
            ->get()
            ->filter(function ($keluar) use ($kategori, $search) {
                return (! $kategori || ($keluar->item && $keluar->item->id_kategori == $kategori))
                    && (! $search || str_contains(mb_strtolower($keluar->item->nama_barang ?? ''), mb_strtolower($search))
                        || str_contains(mb_strtolower($keluar->item->kode_barang ?? ''), mb_strtolower($search))
                        || str_contains(mb_strtolower($keluar->penerima ?? ''), mb_strtolower($search)));
            })
            ->map(function ($keluar) {
                return [
                    'tanggal' => $keluar->tanggal_keluar ? Carbon::parse($keluar->tanggal_keluar)->format('d/m/Y H:i:s') : '-',
                    'raw_date' => $keluar->tanggal_keluar,
                    'jenis' => 'Keluar',
                    'kode_barang' => $keluar->item->kode_barang ?? '-',
                    'nama_barang' => $keluar->item->nama_barang ?? '-',
                    'harga_satuan' => (float) ($keluar->harga_jual ?? 0),
                    'jumlah' => (int) $keluar->jumlah_keluar,
                    'lokasi' => $keluar->lokasi->nama_lokasi ?? '-',
                    'pihak' => $keluar->penerima ?? '-',
                ];
            });

        return $arusMasuk->merge($arusKeluar)
            ->sortBy('raw_date')
            ->map(function ($row) {
                unset($row['raw_date']);

                return $row;
            })
            ->values();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Jenis',
            'Kode Barang',
            'Nama Barang',
            'Harga Satuan',
            'Jumlah',
            'Lokasi',
            'Pihak',
        ];
    }
}

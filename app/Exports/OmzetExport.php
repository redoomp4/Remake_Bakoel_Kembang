<?php

namespace App\Exports;

use App\Models\BarangKeluar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OmzetExport implements FromCollection, WithHeadings
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): Collection
    {
        $userId = Auth::id() ?? optional($this->request->user())->id;

        $startDate = $this->request->filled('start_date') ? Carbon::parse($this->request->start_date)->startOfDay() : null;
        $endDate = $this->request->filled('end_date') ? Carbon::parse($this->request->end_date)->endOfDay() : null;
        $search = $this->request->input('search', $this->request->input('nama_barang'));

        $query = BarangKeluar::with(['item', 'lokasi', 'kondisi'])
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($this->request->filled('lokasi'), fn ($q) => $q->where('id_lokasi', (int) $this->request->lokasi))
            ->when($this->request->filled('kondisi'), fn ($q) => $q->where('id_kondisi', (int) $this->request->kondisi))
            ->when($this->request->filled('kode_barang'), fn ($q) => $q->whereHas('item', fn ($q2) => $q2->where('kode_barang', 'like', "%{$this->request->kode_barang}%")))
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('item', fn ($q2) => $q2->where('nama_barang', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%")
                    )->orWhere('catatan', 'like', "%{$search}%")
                        ->orWhere('penerima', 'like', "%{$search}%");
                });
            })
            ->when($startDate, fn ($q) => $q->where('tanggal_keluar', '>=', $startDate))
            ->when($endDate, fn ($q) => $q->where('tanggal_keluar', '<=', $endDate))
            ->orderByDesc('tanggal_keluar')
            ->get();

        $totalQty = (int) $query->sum('jumlah_keluar');
        $totalOmzet = (float) $query->sum(fn ($row) => (float) ($row->total_harga_jual ?: ($row->jumlah_keluar * $row->harga_jual)));

        $mapped = $query->map(function ($row, $i) {
            $tanggal = '-';
            if (! empty($row->tanggal_keluar)) {
                try {
                    $tanggal = Carbon::parse($row->tanggal_keluar)->format('d/m/Y H:i:s');
                } catch (\Throwable $e) {
                    $tanggal = (string) $row->tanggal_keluar;
                }
            }

            $omzet = (float) ($row->total_harga_jual ?: ($row->jumlah_keluar * $row->harga_jual));

            return [
                'No' => $i + 1,
                'Nama Barang' => optional($row->item)->nama_barang ?? '-',
                'Tanggal' => $tanggal,
                'Lokasi' => optional($row->lokasi)->nama_lokasi ?? '-',
                'Kondisi' => optional($row->kondisi)->nama_kondisi ?? '-',
                'Jumlah Keluar' => (int) $row->jumlah_keluar,
                'Harga Jual' => (float) $row->harga_jual,
                'Omzet' => $omzet,
            ];
        });

        // Tambah baris total di akhir
        $mapped->push([
            'No' => '',
            'Nama Barang' => '',
            'Tanggal' => '',
            'Lokasi' => '',
            'Kondisi' => '',
            'Jumlah Keluar' => $totalQty,
            'Harga Jual' => 'Total Omzet',
            'Omzet' => $totalOmzet,
        ]);

        return $mapped;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Barang',
            'Tanggal',
            'Lokasi',
            'Kondisi',
            'Jumlah Keluar',
            'Harga Jual',
            'Omzet',
        ];
    }
}

<?php

namespace App\Services;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class BatchInventoryService
{
    /**
     * Bangun ledger batch riil (FIFO) untuk seluruh transaksi user.
     *
     * Setiap BarangMasuk diperlakukan sebagai satu batch/lot. BarangKeluar
     * mengonsumsi batch secara FIFO berdasarkan tanggal masuk, sehingga biaya
     * modal yang dipakai selalu harga beli batch asli, bukan rata-rata.
     *
     * @return array{batches: Collection<int, array<string, mixed>>, outgoings: Collection<int, array<string, mixed>>}
     */
    public function snapshot(int $userId, CarbonInterface|string|null $asOf = null): array
    {
        $cutoff = $this->cutoff($asOf);

        $incoming = BarangMasuk::query()
            ->with(['item.satuan', 'lokasi', 'kondisi', 'pemasok', 'user'])
            ->where('user_id', $userId)
            ->get()
            ->filter(fn (BarangMasuk $bm) => ! $cutoff || $this->date($bm->tanggal_masuk)->lte($cutoff));

        $outgoing = BarangKeluar::query()
            ->with(['item.satuan', 'lokasi', 'kondisi', 'user'])
            ->where('user_id', $userId)
            ->get()
            ->filter(fn (BarangKeluar $bk) => ! $cutoff || $this->date($bk->tanggal_keluar)->lte($cutoff));

        $groups = [];

        foreach ($incoming as $bm) {
            $groups[$this->groupKey($bm->item_id, $bm->id_lokasi, $bm->id_kondisi)]['in'][] = $bm;
        }

        foreach ($outgoing as $bk) {
            $groups[$this->groupKey($bk->item_id, $bk->id_lokasi, $bk->id_kondisi)]['out'][] = $bk;
        }

        $batchRows = collect();
        $outRows = collect();

        foreach ($groups as $group) {
            $batches = collect($group['in'] ?? [])
                ->sortBy(fn (BarangMasuk $bm) => $this->sortKey($bm->tanggal_masuk, $bm->id))
                ->values()
                ->map(function (BarangMasuk $bm) {
                    return [
                        'model' => $bm,
                        'remaining' => (int) $bm->jumlah,
                        'consumed' => 0,
                    ];
                })
                ->all();

            $sales = collect($group['out'] ?? [])
                ->sortBy(fn (BarangKeluar $bk) => $this->sortKey($bk->tanggal_keluar, $bk->id))
                ->values();

            foreach ($sales as $sale) {
                $need = (int) $sale->jumlah_keluar;
                $price = (float) ($sale->harga_jual ?? 0);
                $revenue = $need * $price;
                $allocations = [];

                foreach ($batches as $index => $batch) {
                    if ($need <= 0) {
                        break;
                    }

                    if ($batch['remaining'] <= 0) {
                        continue;
                    }

                    $inDate = $this->date($batch['model']->tanggal_masuk);
                    $outDate = $this->date($sale->tanggal_keluar);

                    if ($outDate && $inDate && $inDate->gt($outDate)) {
                        continue;
                    }

                    $take = min($need, $batch['remaining']);
                    if ($take <= 0) {
                        continue;
                    }

                    $unitCost = (float) $batch['model']->harga_satuan;
                    $subtotal = $take * $unitCost;

                    $batches[$index]['remaining'] -= $take;
                    $batches[$index]['consumed'] += $take;

                    $allocations[] = [
                        'barang_masuk_id' => $batch['model']->id,
                        'batch_code' => $this->batchCode($batch['model']),
                        'catatan' => $batch['model']->catatan,
                        'tanggal_masuk' => $this->date($batch['model']->tanggal_masuk),
                        'jumlah' => $take,
                        'harga_beli' => $unitCost,
                        'subtotal' => $subtotal,
                    ];

                    $need -= $take;
                }

                $modal = collect($allocations)->sum('subtotal');

                $outRows->put($sale->id, [
                    'id' => $sale->id,
                    'tanggal' => $this->date($sale->tanggal_keluar),
                    'kode_barang' => $sale->item?->kode_barang ?? '-',
                    'nama_barang' => $sale->item?->nama_barang ?? '-',
                    'kategori_id' => $sale->item?->id_kategori,
                    'satuan' => $sale->item?->satuan?->nama_satuan ?? '-',
                    'lokasi_id' => $sale->id_lokasi,
                    'lokasi' => $sale->lokasi?->nama_lokasi ?? '-',
                    'kondisi_id' => $sale->id_kondisi,
                    'kondisi' => $sale->kondisi?->nama_kondisi ?? '-',
                    'penerima' => $sale->penerima ?? '-',
                    'jumlah_keluar' => (int) $sale->jumlah_keluar,
                    'harga_jual' => $price,
                    'omzet' => $revenue,
                    'allocations' => $allocations,
                    'batch_refs' => collect($allocations)
                        ->map(fn (array $row) => $row['batch_code'].' @ Rp '.number_format($row['harga_beli'], 0, ',', '.').' x '.$row['jumlah'])
                        ->implode('; '),
                    'modal_riil' => (float) $modal,
                    'laba_riil' => (float) ($revenue - $modal),
                    'allocated_qty' => collect($allocations)->sum('jumlah'),
                ]);
            }

            foreach ($batches as $batch) {
                /** @var BarangMasuk $bm */
                $bm = $batch['model'];
                $remaining = max(0, (int) $batch['remaining']);
                $price = (float) $bm->harga_satuan;

                $batchRows->push([
                    'barang_masuk_id' => $bm->id,
                    'batch_code' => $this->batchCode($bm),
                    'catatan' => $bm->catatan,
                    'tanggal_masuk' => $this->date($bm->tanggal_masuk),
                    'kode_barang' => $bm->item?->kode_barang ?? '-',
                    'nama_barang' => $bm->item?->nama_barang ?? '-',
                    'kategori_id' => $bm->item?->id_kategori,
                    'satuan' => $bm->item?->satuan?->nama_satuan ?? '-',
                    'lokasi_id' => $bm->id_lokasi,
                    'lokasi' => $bm->lokasi?->nama_lokasi ?? '-',
                    'kondisi_id' => $bm->id_kondisi,
                    'kondisi' => $bm->kondisi?->nama_kondisi ?? '-',
                    'pemasok' => $bm->pemasok?->nama_pemasok ?? '-',
                    'username' => $bm->user?->username ?? $bm->user?->name ?? '-',
                    'jumlah_masuk' => (int) $bm->jumlah,
                    'jumlah_keluar' => (int) $batch['consumed'],
                    'sisa_stok' => $remaining,
                    'stok_akhir' => $remaining,
                    'harga_beli' => $price,
                    'nilai_aset' => (float) ($remaining * $price),
                    'jumlah_aset' => (float) ($remaining * $price),
                ]);
            }
        }

        return [
            'batches' => $batchRows
                ->sortBy(fn (array $row) => ($row['nama_barang'] ?? '').'|'.($row['batch_code'] ?? ''))
                ->values(),
            'outgoings' => $outRows,
        ];
    }

    public function batches(int $userId, CarbonInterface|string|null $asOf = null): Collection
    {
        return $this->snapshot($userId, $asOf)['batches'];
    }

    public function outgoings(int $userId, CarbonInterface|string|null $asOf = null): Collection
    {
        return $this->snapshot($userId, $asOf)['outgoings'];
    }

    public function outgoing(int $userId, int $barangKeluarId): ?array
    {
        return $this->outgoings($userId)->get($barangKeluarId);
    }

    public function stockValue(int $userId, CarbonInterface|string|null $asOf = null): float
    {
        return (float) $this->batches($userId, $asOf)->sum('nilai_aset');
    }

    private function cutoff(CarbonInterface|string|null $asOf): ?CarbonInterface
    {
        if (! $asOf) {
            return null;
        }

        return $asOf instanceof CarbonInterface
            ? $asOf->copy()->endOfDay()
            : Carbon::parse($asOf)->endOfDay();
    }

    private function date(mixed $value): ?CarbonInterface
    {
        if (! $value) {
            return null;
        }

        return $value instanceof CarbonInterface ? $value : Carbon::parse($value);
    }

    private function sortKey(mixed $date, int $id): int
    {
        $parsed = $this->date($date);

        return (($parsed?->getTimestamp() ?? 0) * 100000) + $id;
    }

    private function groupKey(int $itemId, int $lokasiId, int $kondisiId): string
    {
        return $itemId.'|'.$lokasiId.'|'.$kondisiId;
    }

    private function batchCode(BarangMasuk $bm): string
    {
        return 'BM-'.str_pad((string) $bm->id, 4, '0', STR_PAD_LEFT);
    }
}

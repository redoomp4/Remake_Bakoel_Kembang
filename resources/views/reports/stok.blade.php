@extends('layouts.app')

@push('styles')
    @include('reports.partials.accordion-style')
@endpush

@section('content')
    @php
        $items = $data ?? $grouped ?? collect();
        $totalBatches = $totalBatches ?? ($items instanceof \Illuminate\Pagination\LengthAwarePaginator ? $items->total() : count($items));
        $sortBy = request('sort_by', 'nama_barang');
        $sortDir = request('sort_dir', 'asc');
        $lokasiList = $lokasis ?? $listLokasi ?? [];
        $isPaginator = $items instanceof \Illuminate\Pagination\LengthAwarePaginator;
        $role = auth()->user()?->role ?? 'penjual';

        $sortRoute = function (string $col) use ($sortBy, $sortDir) {
            $nextDir = ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort_by' => $col, 'sort_dir' => $nextDir]);
        };
    @endphp

    <div class="container report-shell">
        <!-- Header Ramah Lansia -->
        <header class="report-header">
            <div>
                <h1>📦 Laporan Stok & Aset Bunga</h1>
                <p>Pantau sisa stok nyata dan total modal tersimpan berdasarkan nota belanja riil.</p>
            </div>
            @if (in_array($role, ['penjual', 'superadmin']))
                <a href="{{ route('laporan.arus') }}" class="report-back">
                    <span>⇄</span> Lihat Arus Masuk & Keluar
                </a>
            @endif
        </header>

        <!-- Filter Sederhana -->
        <form method="GET" class="report-filter" aria-label="Saring data stok bunga">
            <div class="field">
                <label for="start_date">📅 Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="field">
                <label for="end_date">📅 Tanggal Selesai</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="field">
                <label for="nama_barang">🌸 Nama Bunga</label>
                <input type="text" id="nama_barang" name="nama_barang" placeholder="Ketik nama bunga..." value="{{ request('nama_barang') }}">
            </div>
            <div class="field">
                <label for="lokasi">📍 Lokasi Simpan</label>
                <select id="lokasi" name="lokasi">
                    <option value="">Semua Lokasi</option>
                    @foreach ($lokasiList as $lok)
                        @php
                            $val = is_array($lok) ? ($lok['id'] ?? $lok['nama_lokasi']) : (is_object($lok) ? ($lok->id ?? $lok->nama_lokasi) : $lok);
                            $label = is_array($lok) ? ($lok['nama_lokasi'] ?? $val) : (is_object($lok) ? ($lok->nama_lokasi ?? $val) : $lok);
                        @endphp
                        <option value="{{ $val }}" @selected((string) request('lokasi') === (string) $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div class="actions">
                <button type="submit" class="report-btn">Terapkan Filter</button>
                <a href="{{ url()->current() }}" class="report-btn report-btn--soft">Reset Filter</a>
            </div>
        </form>

        <!-- Tombol Aksi Export -->
        <div class="report-exports">
            @if (Route::has('laporan.pdf'))
                <a href="{{ route('laporan.pdf', request()->query()) }}" target="_blank" class="report-btn report-btn--pdf">📄 Cetak PDF</a>
            @elseif (Route::has('export.aset.pdf'))
                <a href="{{ route('export.aset.pdf', request()->query()) }}" target="_blank" class="report-btn report-btn--pdf">📄 Cetak PDF</a>
            @endif

            @if (Route::has('laporan.excel'))
                <a href="{{ route('laporan.excel', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="report-btn">📊 Export Excel</a>
                <a href="{{ route('laporan.excel', array_merge(request()->query(), ['format' => 'csv'])) }}" class="report-btn report-btn--soft">📄 Export CSV</a>
            @elseif (Route::has('export.aset.excel'))
                <a href="{{ route('export.aset.excel', request()->query()) }}" class="report-btn">📊 Export Excel</a>
            @endif
        </div>

        <!-- Banner Ringkasan Raksasa -->
        <section class="report-cards" aria-label="Ringkasan aset riil">
            <article class="report-card report-card--omzet">
                <div class="report-card__label">TOTAL NILAI ASET RIIL <span class="sr-only">Total Nilai Aset Riil</span></div>
                <div class="report-card__value">Rp {{ number_format($totalAset ?? 0, 0, ',', '.') }}</div>
                <div class="report-card__sub">Dihitung murni dari sisa stok × harga beli riil per nota</div>
            </article>
            <article class="report-card">
                <div class="report-card__label">JUMLAH JENIS BUNGA</div>
                <div class="report-card__value" style="color: var(--report-emerald-dark);">
                    {{ $isPaginator ? $items->total() : count($items) }}
                </div>
                <div class="report-card__sub">Jenis tanaman/bunga yang masih memiliki stok fisik</div>
            </article>
            <article class="report-card">
                <div class="report-card__label">NOTA / BATCH AKTIF</div>
                <div class="report-card__value" style="color: var(--report-amber);">
                    {{ $totalBatches }}
                </div>
                <div class="report-card__sub">Total nota belanja masuk yang stoknya belum habis terjual</div>
            </article>
        </section>

        <!-- Tabel Utama Induk (Accordion) -->
        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ $sortRoute('nama_barang') }}">
                                Nama Bunga {!! $sortBy === 'nama_barang' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortRoute('total_stok') }}">
                                Total Stok Sisa {!! $sortBy === 'total_stok' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortRoute('total_aset') }}">
                                Total Nilai Aset Riil {!! $sortBy === 'total_aset' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th style="text-align: center; width: 220px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $group)
                        @php
                            $detailId = 'stok-detail-'.$loop->index;
                            $stokVal = $group['total_stok'] ?? $group['stok_akhir'] ?? 0;
                            $asetVal = $group['total_aset'] ?? $group['jumlah_aset'] ?? $group['nilai_aset'] ?? 0;
                            $satuan = $group['satuan'] ?? '';
                            $batches = $group['batches'] ?? [];
                        @endphp
                        <tr class="parent-row">
                            <td>
                                <span class="report-name">{{ $group['nama_barang'] ?? '-' }}</span>
                                <span class="report-meta">
                                    Kode: {{ $group['kode_barang'] ?? '-' }} · {{ count($batches) ?: ($group['batch_count'] ?? 1) }} nota masuk
                                    @if (!empty($group['lokasi'])) · Lokasi: {{ $group['lokasi'] }} @endif
                                </span>
                            </td>
                            <td class="report-quantity">
                                {{ number_format($stokVal, 0, ',', '.') }} {{ $satuan }}
                            </td>
                            <td class="report-value">
                                Rp {{ number_format($asetVal, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="report-toggle" data-report-toggle data-target="{{ $detailId }}" aria-expanded="false" aria-controls="{{ $detailId }}">
                                    <span class="toggle-label">👁️ Lihat Rincian Nota</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Sub-Tabel Lipat (Accordion Detail) -->
                        <tr class="report-detail-row" id="{{ $detailId }}" hidden>
                            <td colspan="4">
                                <div class="report-detail-panel">
                                    <p class="report-detail-title">
                                        📋 Rincian Nota Belanja Masuk — {{ $group['nama_barang'] }}
                                    </p>
                                    <table class="report-detail-table">
                                        <thead>
                                            <tr>
                                                <th>No. Nota / Tanggal</th>
                                                <th>Sisa Stok Batch</th>
                                                <th>Harga Beli Riil</th>
                                                <th>Subtotal Aset</th>
                                                <th>Pemasok</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($batches as $batch)
                                                @php
                                                    $bSisa = $batch['sisa_stok'] ?? $batch['stok_akhir'] ?? 0;
                                                    $bHarga = $batch['harga_beli'] ?? 0;
                                                    $bAset = $batch['nilai_aset'] ?? $batch['jumlah_aset'] ?? ($bSisa * $bHarga);
                                                    $bSatuan = $batch['satuan'] ?? $satuan;
                                                    $tgl = $batch['tanggal_masuk'] ?? null;
                                                    $tglStr = $tgl instanceof \Carbon\CarbonInterface ? $tgl->format('d/m/Y') : ($tgl ? date('d/m/Y', strtotime((string)$tgl)) : '-');
                                                    $notaTitle = !empty($batch['catatan']) ? $batch['catatan'] : $batch['batch_code'];
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong style="color: var(--report-emerald-dark); font-size: 1.05rem;">
                                                            {{ $notaTitle }}
                                                        </strong>
                                                        <div class="report-meta">
                                                            Ref: {{ $batch['batch_code'] }} · Tgl Masuk: {{ $tglStr }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong style="color: var(--report-emerald-dark); font-size: 1.1rem;">
                                                            {{ number_format($bSisa, 0, ',', '.') }}
                                                        </strong> {{ $bSatuan }}
                                                    </td>
                                                    <td>
                                                        Rp {{ number_format($bHarga, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        <strong style="color: var(--report-slate); font-size: 1.05rem;">
                                                            Rp {{ number_format($bAset, 0, ',', '.') }}
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <span style="font-weight: 700; color: #334155;">
                                                            {{ $batch['pemasok'] ?: 'Pemasok Umum' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-3 text-slate-500">
                                                        Belum ada rincian nota aktif untuk bunga ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3">
                                                    <strong>Total Nilai Aset Riil ({{ $group['nama_barang'] }})</strong>
                                                </td>
                                                <td colspan="2">
                                                    <strong style="color: var(--report-emerald-dark); font-size: 1.15rem;">
                                                        Rp {{ number_format($asetVal, 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="report-empty">
                                🌸 Belum ada data stok bunga yang tersedia untuk saringan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($isPaginator && $items->hasPages())
            <div class="report-pagination">
                <div class="summary">
                    Menampilkan data bunga ke-{{ $items->firstItem() ?? 0 }} sampai {{ $items->lastItem() ?? 0 }} dari total {{ $items->total() }} jenis
                </div>
                {!! $items->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-report-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const targetId = button.getAttribute('data-target');
                    const panel = document.getElementById(targetId);
                    if (!panel) return;

                    const isOpen = button.getAttribute('aria-expanded') === 'true';
                    button.setAttribute('aria-expanded', String(!isOpen));
                    button.classList.toggle('is-open', !isOpen);
                    panel.hidden = isOpen;

                    const label = button.querySelector('.toggle-label');
                    if (label) {
                        label.textContent = isOpen ? '👁️ Lihat Rincian Nota' : '🙈 Tutup Rincian';
                    }
                });
            });
        });
    </script>
@endpush

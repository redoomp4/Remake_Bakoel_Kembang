@extends('layouts.app')

@push('styles')
    @include('reports.partials.accordion-style')
@endpush

@section('content')
    @php
        $omzetData = $omzetData ?? $data ?? $paginated ?? collect();
        $rows = $omzetData;
        $omzetTotal = $total_omzet ?? $totalOmzet ?? 0;
        $modalTotal = $total_modal ?? $totalModal ?? 0;
        $labaTotal = $total_laba ?? $totalLaba ?? 0;
        $isPaginator = $omzetData instanceof \Illuminate\Pagination\LengthAwarePaginator;
        $transactionCount = $isPaginator ? $omzetData->total() : count($rows);
        $averageOmzet = $transactionCount > 0 ? $omzetTotal / $transactionCount : 0;
        $sortBy = request('sort_by', 'tanggal');
        $sortDir = request('sort_dir', 'desc');

        $sortRoute = function (string $col) use ($sortBy, $sortDir) {
            $nextDir = ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc';
            return request()->fullUrlWithQuery(['sort_by' => $col, 'sort_dir' => $nextDir]);
        };
    @endphp

    <div class="container report-shell">
        <!-- Header Ramah Lansia -->
        <header class="report-header">
            <div>
                <h1>💰 Laporan Omzet & Keuntungan Riil</h1>
                <p>Omzet penjualan dan laba bersih riil dihitung langsung dari harga jual dikurangi modal beli nota aslinya.</p>
            </div>
            @if (Route::has('laporan'))
                <a href="{{ route('laporan') }}" class="report-back">
                    <span>←</span> Kembali ke Laporan Stok
                </a>
            @endif
        </header>

        <!-- Filter Sederhana -->
        <form method="GET" class="report-filter" aria-label="Saring data omzet">
            <div class="field">
                <label for="start_date">📅 Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="field">
                <label for="end_date">📅 Tanggal Selesai</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="field">
                <label for="search">🌸 Cari Barang / Catatan</label>
                <input type="text" id="search" name="search" placeholder="Ketik nama atau kode bunga..." value="{{ request('search') }}">
            </div>
            @if (isset($lokasis) && count($lokasis))
                <div class="field">
                    <label for="lokasi">📍 Lokasi Gudang</label>
                    <select id="lokasi" name="lokasi">
                        <option value="">Semua Lokasi</option>
                        @foreach ($lokasis as $lok)
                            @php
                                $lokId = is_object($lok) ? ($lok->id ?? $lok->nama_lokasi) : (is_array($lok) ? ($lok['id'] ?? $lok['nama_lokasi']) : $lok);
                                $lokName = is_object($lok) ? ($lok->nama_lokasi ?? $lokId) : (is_array($lok) ? ($lok['nama_lokasi'] ?? $lokId) : $lok);
                            @endphp
                            <option value="{{ $lokId }}" @selected((string) request('lokasi') === (string) $lokId)>{{ $lokName }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if (isset($kondisis) && count($kondisis))
                <div class="field">
                    <label for="kondisi">🌿 Kondisi Bunga</label>
                    <select id="kondisi" name="kondisi">
                        <option value="">Semua Kondisi</option>
                        @foreach ($kondisis as $kon)
                            @php
                                $konId = is_object($kon) ? ($kon->id ?? $kon->nama_kondisi) : (is_array($kon) ? ($kon['id'] ?? $kon['nama_kondisi']) : $kon);
                                $konName = is_object($kon) ? ($kon->nama_kondisi ?? $konId) : (is_array($kon) ? ($kon['nama_kondisi'] ?? $konId) : $kon);
                            @endphp
                            <option value="{{ $konId }}" @selected((string) request('kondisi') === (string) $konId)>{{ $konName }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div class="actions">
                <button type="submit" class="report-btn">Terapkan Filter</button>
                <a href="{{ url()->current() }}" class="report-btn report-btn--soft">Reset Filter</a>
            </div>
        </form>

        <!-- Tombol Aksi Export -->
        <div class="report-exports">
            @if (Route::has('export.omzet.pdf'))
                <a href="{{ route('export.omzet.pdf', request()->query()) }}" target="_blank" class="report-btn report-btn--pdf">📄 Cetak PDF</a>
            @elseif (Route::has('laporan.arus.pdf'))
                <a href="{{ route('laporan.arus.pdf', request()->query()) }}" target="_blank" class="report-btn report-btn--pdf">📄 Cetak PDF</a>
            @endif

            @if (Route::has('export.omzet.excel'))
                <a href="{{ route('export.omzet.excel', request()->query()) }}" class="report-btn">📊 Export Excel</a>
            @elseif (Route::has('laporan.arus.excel'))
                <a href="{{ route('laporan.arus.excel', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="report-btn">📊 Export Excel</a>
            @endif
        </div>

        <!-- Banner Ringkasan Raksasa di Atas -->
        <section class="report-cards" aria-label="Ringkasan omzet dan laba">
            <article class="report-card report-card--omzet">
                <div class="report-card__label">🟢 TOTAL OMZET RIIL <span class="sr-only">Total Omzet Riil</span></div>
                <div class="report-card__value">Rp {{ number_format($omzetTotal, 0, ',', '.') }}</div>
                <div class="report-card__sub">Total pemasukan bersih dari seluruh transaksi penjualan</div>
            </article>
            <article class="report-card report-card--laba">
                <div class="report-card__label">💙 TOTAL LABA BERSIH RIIL <span class="sr-only">Total Laba Bersih Riil</span></div>
                <div class="report-card__value">Rp {{ number_format($labaTotal, 0, ',', '.') }}</div>
                <div class="report-card__sub">Keuntungan riil = Total Omzet − Modal Beli Riil Batch</div>
            </article>
            <article class="report-card">
                <div class="report-card__label">TOTAL MODAL RIIL <span class="sr-only">Total Modal / HPP Riil</span></div>
                <div class="report-card__value" style="color: var(--report-amber);">Rp {{ number_format($modalTotal, 0, ',', '.') }}</div>
                <div class="report-card__sub">{{ $transactionCount }} transaksi penjualan · rata-rata Rp {{ number_format($averageOmzet, 0, ',', '.') }}</div>
            </article>
        </section>

        <!-- Tabel Utama (Induk) -->
        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th>
                            <a href="{{ $sortRoute('tanggal') }}">
                                Tanggal {!! $sortBy === 'tanggal' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortRoute('nama_barang') }}">
                                Nama Item {!! $sortBy === 'nama_barang' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortRoute('jumlah_keluar') }}">
                                Jumlah {!! $sortBy === 'jumlah_keluar' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortRoute('harga_jual') }}">
                                Harga Jual Riil {!! $sortBy === 'harga_jual' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th>
                            <a href="{{ $sortRoute('omzet_item') }}">
                                Total Omzet {!! $sortBy === 'omzet_item' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
                            </a>
                        </th>
                        <th style="text-align: center; width: 230px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $item)
                        @php
                            $detailId = 'omzet-detail-'.$loop->index;
                            $allocations = $item['allocations'] ?? [];
                            $hpp = (float) ($item['modal_riil'] ?? collect($allocations)->sum('subtotal'));
                            $omzetItem = (float) ($item['omzet_item'] ?? $item['omzet'] ?? 0);
                            $labaItem = (float) ($item['laba_riil'] ?? ($omzetItem - $hpp));
                            $qty = (int) ($item['jumlah_keluar'] ?? $item['jumlah'] ?? 0);
                            $tgl = $item['tanggal'] ?? null;
                            $tglStr = $tgl instanceof \Carbon\CarbonInterface ? $tgl->format('d/m/Y') : ($tgl ? date('d/m/Y', strtotime((string)$tgl)) : '-');
                            $hargaJual = (float) ($item['harga_jual'] ?? 0);
                        @endphp
                        <tr class="parent-row">
                            <td>
                                <strong style="font-size: 1.05rem; color: #1e293b;">{{ $tglStr }}</strong>
                            </td>
                            <td>
                                <span class="report-name">{{ $item['nama_barang'] ?? '-' }}</span>
                                <span class="report-meta">
                                    Kode: {{ $item['kode_barang'] ?? '-' }}
                                    @if (!empty($item['lokasi'])) · {{ $item['lokasi'] }} @endif
                                    @if (!empty($item['kondisi'])) · {{ $item['kondisi'] }} @endif
                                </span>
                            </td>
                            <td class="report-quantity">
                                {{ number_format($qty, 0, ',', '.') }} {{ $item['satuan'] ?? '' }}
                            </td>
                            <td>
                                Rp {{ number_format($hargaJual, 0, ',', '.') }}
                            </td>
                            <td class="report-value">
                                Rp {{ number_format($omzetItem, 0, ',', '.') }}
                            </td>
                            <td style="text-align: center;">
                                <button type="button" class="report-toggle" data-report-toggle data-target="{{ $detailId }}" aria-expanded="false" aria-controls="{{ $detailId }}">
                                    <span class="toggle-label">🔍 Detail Modal Beli</span>
                                </button>
                            </td>
                        </tr>

                        <!-- Sub-Tabel Lipat (Detail Expand Modal Beli) -->
                        <tr class="report-detail-row" id="{{ $detailId }}" hidden>
                            <td colspan="6">
                                <div class="report-detail-panel">
                                    <p class="report-detail-title">
                                        🔍 Rincian Modal Beli Riil (Batch Nota Masuk) & Keuntungan
                                    </p>
                                    <table class="report-detail-table">
                                        <thead>
                                            <tr>
                                                <th>No. Nota / Tanggal Masuk</th>
                                                <th>Qty Diambil</th>
                                                <th>Harga Beli Batch</th>
                                                <th>Subtotal Modal (HPP)</th>
                                                <th>Laba Bersih Batch</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($allocations as $alloc)
                                                @php
                                                    $allocQty = (int) ($alloc['jumlah'] ?? 0);
                                                    $beli = (float) ($alloc['harga_beli'] ?? 0);
                                                    $subtotalModal = (float) ($alloc['subtotal'] ?? ($allocQty * $beli));
                                                    $labaBatch = ($hargaJual - $beli) * $allocQty;
                                                    $allocTgl = $alloc['tanggal_masuk'] ?? null;
                                                    $allocTglStr = $allocTgl instanceof \Carbon\CarbonInterface ? $allocTgl->format('d/m/Y') : ($allocTgl ? date('d/m/Y', strtotime((string)$allocTgl)) : '-');
                                                    $allocTitle = !empty($alloc['catatan']) ? $alloc['catatan'] : $alloc['batch_code'];
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong style="color: var(--report-emerald-dark); font-size: 1.05rem;">
                                                            {{ $allocTitle }}
                                                        </strong>
                                                        <div class="report-meta">
                                                            Ref: {{ $alloc['batch_code'] }} · Tgl Masuk: {{ $allocTglStr }}
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <strong style="font-size: 1.1rem; color: #1e293b;">
                                                            {{ number_format($allocQty, 0, ',', '.') }}
                                                        </strong> {{ $item['satuan'] ?? '' }}
                                                    </td>
                                                    <td>
                                                        Rp {{ number_format($beli, 0, ',', '.') }}
                                                    </td>
                                                    <td>
                                                        <strong style="color: var(--report-amber); font-size: 1.05rem;">
                                                            Rp {{ number_format($subtotalModal, 0, ',', '.') }}
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <strong style="color: var(--report-blue); font-size: 1.1rem;">
                                                            Rp {{ number_format($labaBatch, 0, ',', '.') }}
                                                        </strong>
                                                        <div class="report-meta">
                                                            (Rp {{ number_format($hargaJual, 0, ',', '.') }} − Rp {{ number_format($beli, 0, ',', '.') }}) × {{ $allocQty }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-3 text-slate-500">
                                                        Belum ada rincian alokasi batch nota untuk transaksi ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="3">
                                                    <strong>Total Modal Beli Riil (HPP Transaksi Ini)</strong>
                                                </td>
                                                <td colspan="2">
                                                    <strong style="color: var(--report-amber); font-size: 1.1rem;">
                                                        Rp {{ number_format($hpp, 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <strong>Total Laba Bersih Riil (Total Omzet − Total Modal)</strong>
                                                </td>
                                                <td colspan="2">
                                                    <strong style="color: var(--report-blue); font-size: 1.2rem;">
                                                        Rp {{ number_format($labaItem, 0, ',', '.') }}
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
                            <td colspan="6" class="report-empty">
                                💰 Belum ada transaksi penjualan bunga yang tercatat untuk saringan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($isPaginator && $omzetData->hasPages())
            <div class="report-pagination mt-6">
                <div class="summary mb-3 text-sm font-semibold text-slate-600">
                    Menampilkan data transaksi ke-{{ $omzetData->firstItem() ?? 0 }} sampai {{ $omzetData->lastItem() ?? 0 }} dari total {{ $omzetData->total() }} transaksi
                </div>
                {{ $omzetData->links() }}
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
                        label.textContent = isOpen ? '🔍 Detail Modal Beli' : '🙈 Tutup Detail';
                    }
                });
            });
        });
    </script>
@endpush

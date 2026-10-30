@extends('layouts.app')

@push('styles')
    @include('reports.partials.accordion-style')
@endpush

@section('content')
    @php
        $sortUrl = fn (string $field) => route('laporan.arus', array_merge(request()->all(), [
            'sort_by' => $field,
            'sort_dir' => request('sort_by') === $field && request('sort_dir') === 'asc' ? 'desc' : 'asc',
        ]));
    @endphp

    <div class="container report-shell">
        <header class="report-header">
            <div>
                <h1>Laporan Arus Barang</h1>
                <p>Pantau barang masuk dan keluar. Klik detail untuk melihat nota/batch asal stok.</p>
            </div>
            <a href="{{ route('laporan') }}" class="report-back">← Kembali ke Laporan Stok</a>
        </header>

        <form class="report-filter" method="GET" action="{{ route('laporan.arus') }}">
            <div class="field">
                <label for="start_date">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="field">
                <label for="end_date">Tanggal Selesai</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>
            <div class="field">
                <label for="lokasi">Lokasi</label>
                <select id="lokasi" name="lokasi">
                    <option value="">Semua Lokasi</option>
                    @foreach ($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}" @selected((string) request('lokasi') === (string) $lokasi->id)>{{ $lokasi->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="search">Pencarian</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nama / kode / pihak...">
            </div>
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
            <div class="actions">
                <button type="submit" class="report-btn">Terapkan Filter</button>
                <a href="{{ route('laporan.arus') }}" class="report-btn report-btn--soft">Reset</a>
            </div>
        </form>

        <div class="report-exports">
            <a href="{{ route('laporan.arus.pdf', request()->query()) }}" target="_blank" class="report-btn report-btn--pdf">📄 Cetak PDF</a>
            <a href="{{ route('laporan.arus.excel', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="report-btn">📊 Export Excel</a>
            <a href="{{ route('laporan.arus.excel', array_merge(request()->query(), ['format' => 'csv'])) }}" class="report-btn">📄 Export CSV</a>
        </div>

        <section class="report-cards" aria-label="Ringkasan arus barang">
            <article class="report-card report-card--omzet">
                <div class="report-card__label">🟢 TOTAL OMZET RIIL <span class="sr-only">Total Omzet Riil</span></div>
                <div class="report-card__value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</div>
                <div class="report-card__sub">Dari transaksi barang keluar</div>
            </article>
            <article class="report-card report-card--laba">
                <div class="report-card__label">💙 TOTAL LABA BERSIH RIIL <span class="sr-only">Total Laba Bersih Riil</span></div>
                <div class="report-card__value">Rp {{ number_format($totalLaba, 0, ',', '.') }}</div>
                <div class="report-card__sub">Omzet riil − modal batch riil</div>
            </article>
            <article class="report-card">
                <div class="report-card__label">TOTAL MODAL RIIL <span class="sr-only">Total Modal / HPP Riil</span></div>
                <div class="report-card__value" style="color: var(--report-amber);">Rp {{ number_format($totalModal, 0, ',', '.') }}</div>
                <div class="report-card__sub">Harga beli batch yang benar-benar terpakai</div>
            </article>
        </section>

        <div class="report-table-wrap">
            <table class="report-table">
                <thead>
                    <tr>
                        <th><a href="{{ $sortUrl('tanggal') }}">Tanggal</a></th>
                        <th><a href="{{ $sortUrl('nama_barang') }}">Nama Barang</a></th>
                        <th><a href="{{ $sortUrl('jenis') }}">Jenis</a></th>
                        <th><a href="{{ $sortUrl('jumlah_masuk') }}">Jumlah</a></th>
                        <th><a href="{{ $sortUrl('harga_jual') }}">Harga</a></th>
                        <th><a href="{{ $sortUrl('omzet') }}">Total</a></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paginated as $row)
                        @php
                            $detailId = 'flow-detail-'.$loop->index;
                            $allocations = $row['allocations'] ?? [];
                            $isOut = $row['jenis'] === 'Keluar';
                            $unitPrice = $isOut ? $row['harga_jual'] : $row['harga_beli'];
                            $totalValue = $row['total_nilai'] ?? ($isOut ? ($row['omzet'] ?? 0) : (($row['harga_beli'] ?? 0) * ($row['jumlah'] ?? 0)));
                            $pihak = $row['pihak'] ?? '-';
                        @endphp
                        <tr class="parent-row">
                            <td>{{ $row['tanggal']?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                <span class="report-name">{{ $row['nama_barang'] ?? '-' }}</span>
                                <span class="report-meta">{{ $row['kode_barang'] ?? '-' }} · {{ $row['lokasi'] ?? '-' }} · {{ $pihak }}</span>
                            </td>
                            <td>
                                <span class="report-badge {{ $isOut ? 'report-badge--out' : 'report-badge--in' }}">{{ $row['jenis'] }}</span>
                            </td>
                            <td class="report-quantity">{{ number_format($row['jumlah'] ?? 0, 0, ',', '.') }} {{ $row['satuan'] ?? '' }}</td>
                            <td>{{ $unitPrice !== null ? 'Rp '.number_format((float) $unitPrice, 0, ',', '.') : '-' }}</td>
                            <td class="report-value">Rp {{ number_format((float) $totalValue, 0, ',', '.') }}</td>
                            <td>
                                <button type="button" class="report-toggle" data-report-toggle data-target="{{ $detailId }}" aria-expanded="false" aria-controls="{{ $detailId }}">
                                    <span class="toggle-label">🔍 Detail Batch</span>
                                </button>
                            </td>
                        </tr>
                        <tr class="report-detail-row" id="{{ $detailId }}" hidden>
                            <td colspan="7">
                                <div class="report-detail-panel">
                                    <p class="report-detail-title">{{ $isOut ? 'Batch Asal Stok (FIFO)' : 'Nota Barang Masuk' }} — {{ $row['nama_barang'] ?? '-' }}</p>
                                    <table class="report-detail-table">
                                        <thead>
                                            <tr>
                                                <th>No. Nota / Tanggal</th>
                                                <th>Qty</th>
                                                <th>Harga Beli Riil</th>
                                                <th>Subtotal HPP</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($allocations as $allocation)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $allocation['batch_code'] ?? '-' }}</strong>
                                                        <div class="report-meta">{{ !empty($allocation['tanggal_masuk']) ? $allocation['tanggal_masuk']->format('d M Y') : '-' }}</div>
                                                    </td>
                                                    <td>{{ number_format($allocation['jumlah'] ?? 0, 0, ',', '.') }} {{ $row['satuan'] ?? '' }}</td>
                                                    <td>Rp {{ number_format($allocation['harga_beli'] ?? 0, 0, ',', '.') }}</td>
                                                    <td><strong>Rp {{ number_format($allocation['subtotal'] ?? 0, 0, ',', '.') }}</strong></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4">Tidak ada rincian batch untuk transaksi ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            @if ($isOut)
                                                <tr>
                                                    <td colspan="3">Total HPP Batch</td>
                                                    <td>Rp {{ number_format((float) ($row['modal_riil'] ?? 0), 0, ',', '.') }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">Laba Bersih (Omzet − HPP)</td>
                                                    <td>Rp {{ number_format((float) ($row['laba_riil'] ?? 0), 0, ',', '.') }}</td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td colspan="3">Total Pembelian Batch</td>
                                                    <td>Rp {{ number_format((float) $totalValue, 0, ',', '.') }}</td>
                                                </tr>
                                            @endif
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="report-empty">Belum ada arus barang untuk filter ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="report-pagination">
            <div class="summary">
                Menampilkan {{ $paginated->firstItem() ?? 0 }}–{{ $paginated->lastItem() ?? 0 }} dari {{ $paginated->total() }} transaksi
            </div>
            {!! $paginated->appends(request()->query())->links('pagination::bootstrap-5') !!}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-report-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    const panel = document.getElementById(button.dataset.target);
                    if (!panel) return;
                    const isOpen = button.getAttribute('aria-expanded') === 'true';
                    button.setAttribute('aria-expanded', String(!isOpen));
                    button.classList.toggle('is-open', !isOpen);
                    panel.hidden = isOpen;
                    const label = button.querySelector('.toggle-label');
                    if (label) label.textContent = isOpen ? '🔍 Detail Batch' : '🙈 Tutup Detail';
                });
            });
        });
    </script>
@endpush

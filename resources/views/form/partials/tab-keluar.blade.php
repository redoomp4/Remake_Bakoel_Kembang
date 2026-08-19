<section id="tab-content-keluar" class="oneshot-tab-content hidden" role="tabpanel">
    <form action="{{ route('barang-keluar.store') }}" method="POST" class="rounded-3xl border border-brand-accent bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <h2 class="mb-4 text-xl font-black text-brand-emerald">Transaksi Barang Keluar</h2>
        <div class="mb-5 flex min-h-8 items-center">
            <span id="live-stock-badge" class="text-xs font-black text-brand-slate" aria-live="polite"></span>
        </div>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="field-label">Pilih Barang</label>
                <select id="select-item-outbound" name="kode_barang" required class="field-input select-item-kode">
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($items ?? [] as $item)
                        <option value="{{ $item->kode_barang }}">{{ $item->kode_barang }} — {{ $item->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label">Lokasi Asal</label>
                <select name="id_lokasi" required class="field-input select-lokasi">
                    <option value="">-- Pilih Lokasi --</option>
                    @foreach ($lokasis ?? [] as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_lokasi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label">Kondisi Barang</label>
                <select name="id_kondisi" required class="field-input select-kondisi">
                    <option value="">-- Pilih Kondisi --</option>
                    @foreach ($kondisis ?? [] as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_kondisi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label">Jumlah Keluar</label>
                <input name="jumlah_keluar" type="number" min="1" required class="field-input">
            </div>
            <div>
                <label class="field-label">Harga Jual</label>
                <input id="input-harga-jual" name="harga_jual" type="number" min="0" step="0.01" required class="field-input">
            </div>
            <div>
                <label class="field-label">Penerima</label>
                <input name="penerima" required class="field-input">
            </div>
            <div>
                <label class="field-label">Lokasi Tujuan</label>
                <input name="lokasi_tujuan" class="field-input">
            </div>
            <div class="md:col-span-2">
                <label class="field-label">Catatan</label>
                <textarea name="catatan" rows="2" class="field-input"></textarea>
            </div>
        </div>
        <button type="submit" class="mt-6 rounded-xl bg-brand-emerald px-5 py-3 text-sm font-black text-white transition hover:bg-brand-slate">
            Simpan Transaksi Keluar
        </button>
    </form>

    {{-- Script khusus tab keluar: live stock --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const outboundSelect = document.getElementById('select-item-outbound');
            const stockBadge = document.getElementById('live-stock-badge');
            const hargaJualInput = document.getElementById('input-harga-jual');

            if (!outboundSelect) return;

            outboundSelect.addEventListener('change', async (e) => {
                const code = e.target.value;
                if (!code) {
                    if (stockBadge) stockBadge.textContent = '';
                    return;
                }

                try {
                    const response = await fetch(`/api/form/item-detail/${encodeURIComponent(code)}`, {
                        headers: { Accept: 'application/json' }
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error();

                    const item = result.data;
                    if (hargaJualInput) {
                        hargaJualInput.value = item.harga_dasar ?? '';
                    }
                    if (stockBadge) {
                        stockBadge.className =
                            `rounded-full px-3 py-1 text-xs font-black ${item.stok > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}`;
                        stockBadge.textContent = item.stok > 0 ? `Sisa Stok: ${item.stok}` : 'Stok Habis (0)';
                    }
                } catch {
                    if (stockBadge) {
                        stockBadge.textContent = 'Stok tidak dapat dimuat';
                        stockBadge.className = 'text-xs font-bold text-rose-600';
                    }
                }
            });
        });
    </script>
</section>

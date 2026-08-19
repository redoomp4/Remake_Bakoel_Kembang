<section id="tab-content-masuk" class="oneshot-tab-content hidden" role="tabpanel">
    <form action="{{ route('barang-masuk.store') }}" method="POST" class="rounded-3xl border border-brand-accent bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <h2 class="mb-6 text-xl font-black text-brand-emerald">Transaksi Barang Masuk</h2>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="field-label">Pilih Barang</label>
                <select name="kode_barang" required class="field-input select-item-kode">
                    <option value="">-- Pilih Barang --</option>
                    @foreach ($items ?? [] as $item)
                        <option value="{{ $item->kode_barang }}">{{ $item->kode_barang }} — {{ $item->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label">Jumlah Masuk</label>
                <input name="jumlah" type="number" min="1" required class="field-input">
            </div>
            <div>
                <label class="field-label">Harga Satuan</label>
                <input name="harga_satuan" type="number" min="0" step="0.01" required class="field-input">
            </div>
            <div>
                <label class="field-label">Tanggal Masuk</label>
                <input name="tanggal_masuk" type="date" required value="{{ date('Y-m-d') }}" class="field-input">
            </div>
            <div>
                <label class="field-label">Tanggal Kadaluarsa</label>
                <input name="tanggal_kadaluarsa" type="date" class="field-input">
            </div>
            <div>
                <label class="field-label">Pemasok</label>
                <select name="id_pemasok" required class="field-input select-pemasok">
                    <option value="">-- Pilih Pemasok --</option>
                    @foreach ($pemasoks ?? [] as $item)
                        <option value="{{ $item->id }}">{{ $item->nama_pemasok }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="field-label">Lokasi Penyimpanan</label>
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
            <div class="md:col-span-2 lg:col-span-3">
                <label class="field-label">Catatan</label>
                <textarea name="catatan" rows="2" class="field-input"></textarea>
            </div>
        </div>
        <button type="submit" class="mt-6 rounded-xl bg-brand-emerald px-5 py-3 text-sm font-black text-white transition hover:bg-brand-slate">
            Simpan Transaksi Masuk
        </button>
    </form>

</section>

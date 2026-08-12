@extends('layouts.app')
@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">
        <div style="max-width:900px;margin:auto;">
            <div style="margin-bottom:1.5rem;">
                <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;">Inventori · Barang Keluar
                </p>
                <h2 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;">Form Barang Keluar</h2>
            </div>
            @if (session('error'))
                <div style="background:#fef2f2;color:#b91c1c;padding:1rem;border-radius:1rem;margin-bottom:1rem;">
                    {{ session('error') }}</div>
            @elseif(session('success'))
                <div style="background:#ecfdf5;color:#047857;padding:1rem;border-radius:1rem;margin-bottom:1rem;">
                    {{ session('success') }}</div>
            @endif
            <form action="{{ route('barang-keluar.store') }}" method="POST" id="barangKeluarForm"
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                @csrf<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.15rem;">
                    <div style="grid-column:1/-1;"><label for="kode_lokasi_kondisi"
                            style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Pilih Barang (Kode -
                            Lokasi - Kondisi)</label>
                        <div style="display:flex;gap:.5rem;"><select name="kode_lokasi_kondisi" id="kode_lokasi_kondisi"
                                required style="flex:1;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                                <option value="" disabled selected>-- Pilih Barang --</option>
                            </select><button type="button"
                                onclick="alert('Gunakan kamera perangkat untuk memindai nota.');"
                                style="border:0;background:#ecfdf5;color:#047857;border-radius:.75rem;padding:0 1rem;font-weight:800;">📷
                                Scan</button></div>
                        @error('kode_lokasi_kondisi')
                            <p style="color:#ef4444;font-size:.75rem;font-weight:700;">{{ $message }}</p>
                        @enderror
                    </div>
                    <input type="hidden" name="kode_barang" id="kode_barang"><input type="hidden" name="id_lokasi"
                        id="id_lokasi"><input type="hidden" name="id_kondisi" id="id_kondisi">
                    @foreach ([['nama_barang', 'Nama Barang'], ['satuan', 'Satuan'], ['stok_tersedia', 'Stok Tersedia'], ['harga_dasar', 'Harga Rata-Rata']] as $field)
                        <div><label for="{{ $field[0] }}"
                                style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">{{ $field[1] }}</label><input
                                type="text" id="{{ $field[0] }}" readonly
                                style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;background:#f8fafc;">
                        </div>
                    @endforeach
                    <div>
                        <label for="jumlah_keluar"
                            style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Jumlah
                            Keluar</label><input type="number" name="jumlah_keluar" id="jumlah_keluar" required
                            style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                        <div id="jumlah_warning" style="color:#ef4444;font-size:.75rem;margin-top:.3rem;"></div>
                    </div>
                    <div><label for="harga_jual"
                            style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Harga Jual /
                            Unit</label><input type="number" name="harga_jual" id="harga_jual" required
                            style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;"></div>
                    <div><label for="total_harga"
                            style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Total Harga
                            Jual</label><input type="number" id="total_harga" readonly value="0"
                            style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;background:#f8fafc;">
                    </div>
                    <div><label for="catatan"
                            style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Catatan</label><select
                            name="catatan" id="catatan" required
                            style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                            <option value="" disabled selected>-- Pilih Catatan --</option>
                            <option value="Penerimaan Penjualan">Penerimaan Penjualan</option>
                            <option value="Penghapusan">Penghapusan</option>
                            <option value="Retur">Retur</option>
                        </select></div>
                    @foreach ([['penerima', 'Penerima'], ['lokasi_tujuan', 'Lokasi Tujuan']] as $field)
                        <div><label for="{{ $field[0] }}"
                                style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">{{ $field[1] }}</label>
                            <div style="display:flex;gap:.5rem;"><input type="text" name="{{ $field[0] }}"
                                    id="{{ $field[0] }}" required
                                    style="flex:1;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                                @if ($field[0] === 'penerima')
                                    <button type="button" onclick="speakField('penerima')"
                                        style="border:0;background:#ecfdf5;color:#047857;border-radius:.75rem;padding:0 .9rem;font-size:1.2rem;">🎙</button>
                                @endif
                            </div>
                            @error($field[0])
                                <p style="color:#ef4444;font-size:.75rem;font-weight:700;">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div><button type="submit" id="submitBtn"
                    style="margin-top:1.5rem;background:#0B4F35;color:#fff;border:0;padding:.9rem 1.5rem;border-radius:.85rem;font-weight:800;">Simpan
                    Transaksi</button></form>
        </div>
    </div>
    <script>
        function num(v) {
            const n = parseFloat(String(v ?? '').replace(/[^\d.-]/g, ''));
            return isNaN(n) ? 0 : n
        }

        function pickHarga(o) {
            return num(o?.harga_dasar ?? o?.harga ?? o?.harga_satuan ?? o?.item?.harga_dasar)
        }

        function speakField(id) {
            if ('speechSynthesis' in window) speechSynthesis.speak(new SpeechSynthesisUtterance('Masukkan ' + id))
        }
        async function loadPilihanBarang() {
            const res = await fetch("{{ route('barang-keluar.pilihan-barang') }}"),
                data = await res.json(),
                select = document.getElementById('kode_lokasi_kondisi');
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = `${item.kode}|${item.lokasi_id}|${item.kondisi_id}`;
                opt.text = `${item.kode} - ${item.nama_barang} - ${item.lokasi} - ${item.kondisi}`;
                Object.assign(opt.dataset, {
                    nama: item.nama_barang,
                    satuan: item.satuan,
                    stok: item.stok,
                    harga: pickHarga(item)
                });
                select.appendChild(opt)
            })
        }
        async function fetchDetail(k, l, c) {
            const res = await fetch(
                    `{{ route('barang-keluar.detail-barang') }}?kode_barang=${encodeURIComponent(k)}&id_lokasi=${encodeURIComponent(l)}&id_kondisi=${encodeURIComponent(c)}`
                    ),
                d = await res.json();
            nama_barang.value = d.nama_barang ?? '';
            satuan.value = d.satuan ?? '';
            stok_tersedia.value = num(d.stok);
            harga_dasar.value = pickHarga(d)
        }
        document.addEventListener('DOMContentLoaded', () => {
            const s = document.getElementById('kode_lokasi_kondisi'),
                q = document.getElementById('jumlah_keluar'),
                h = document.getElementById('harga_jual'),
                t = document.getElementById('total_harga'),
                b = document.getElementById('submitBtn');
            loadPilihanBarang();
            s.addEventListener('change', async () => {
                const [k, l, c] = s.value.split('|'), o = s.options[s.selectedIndex];
                kode_barang.value = k;
                id_lokasi.value = l;
                id_kondisi.value = c;
                nama_barang.value = o.dataset.nama || '';
                satuan.value = o.dataset.satuan || '';
                stok_tersedia.value = num(o.dataset.stok);
                harga_dasar.value = num(o.dataset.harga);
                await fetchDetail(k, l, c);
                t.value = num(q.value) * num(h.value)
            });

            function update() {
                t.value = num(q.value) * num(h.value);
                b.disabled = num(q.value) < 1 || num(q.value) > num(stok_tersedia.value)
            }
            q.addEventListener('input', update);
            h.addEventListener('input', update)
        })
    </script>
@endsection
<style>
    input:focus,
    select:focus {
        outline: none;
        border-color: #0B4F35 !important;
        box-shadow: 0 0 0 3px rgba(11, 79, 53, .12)
    }
</style>

@extends('layouts.app')
@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">
        <div style="max-width:900px;margin:auto;">
            <div style="margin-bottom:1.5rem;">
                <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;">Inventori · Barang Masuk
                </p>
                <h4 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;">Tambah Barang Masuk</h4>
            </div>
            @if (session('error'))
                <div style="background:#fef2f2;color:#b91c1c;padding:1rem;border-radius:1rem;margin-bottom:1rem;">
                    {{ session('error') }}</div>
                @endif @if (session('success'))
                    <div style="background:#ecfdf5;color:#047857;padding:1rem;border-radius:1rem;margin-bottom:1rem;">
                        {{ session('success') }}</div>
                @endif
                <form method="POST" action="{{ route('barang-masuk.store') }}" onsubmit="return confirmSimpan();"
                    style="background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.2rem;">@csrf<div
                            style="grid-column:1/-1;"><label for="kode_barang"
                                style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Pilih Item</label>
                            <div style="display:flex;gap:.5rem;"><select name="kode_barang" id="kode_barang" required
                                    style="flex:1;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                                    <option value="" disabled selected>-- Pilih Item --</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->kode_barang }}"
                                            {{ old('kode_barang') == $item->kode_barang ? 'selected' : '' }}>
                                            {{ $item->kode_barang }} - {{ $item->nama_barang }}</option>
                                    @endforeach
                                </select>
                                <button type="button" onclick="alert('Gunakan kamera perangkat untuk memindai nota.');"
                                    style="border:0;background:#ecfdf5;color:#047857;border-radius:.75rem;padding:0 1rem;font-weight:800;">📷
                                    Scan Nota</button>
                            </div>
                            @error('kode_barang')
                                <p style="color:#ef4444;font-size:.75rem;font-weight:700;">{{ $message }}</p>
                            @enderror
                        </div>
                        @foreach ([['jumlah', 'Jumlah', 'number'], ['harga_satuan', 'Harga Beli', 'number']] as $field)
                            <div><label for="{{ $field[0] }}"
                                    style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">{{ $field[1] }}</label><input
                                    type="{{ $field[2] }}" id="{{ $field[0] }}" name="{{ $field[0] }}"
                                    min="0" required
                                    style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                                @error($field[0])
                                    <p style="color:#ef4444;font-size:.75rem;font-weight:700;">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                        <div>
                            <label for="total_harga"
                                style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Total
                                Harga</label><input type="number" id="total_harga" name="total_harga" readonly
                                style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;background:#f8fafc;">
                        </div>
                        <div><label for="tanggal_masuk"
                                style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Tanggal
                                Masuk</label><input type="datetime-local" id="tanggal_masuk" name="tanggal_masuk"
                                value="{{ now()->format('Y-m-d\TH:i') }}" required
                                style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;"></div>
                        <div><label for="tanggal_kadaluarsa"
                                style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Tanggal
                                Kadaluarsa</label><input type="datetime-local" id="tanggal_kadaluarsa"
                                name="tanggal_kadaluarsa" value="{{ now()->format('Y-m-d\TH:i') }}" required
                                style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;"></div>
                        @foreach ([['id_pemasok', 'Pemasok', $pemasoks, 'nama_pemasok'], ['id_lokasi', 'Lokasi', $lokasis, 'nama_lokasi'], ['id_kondisi', 'Kondisi', $kondisis, 'nama_kondisi']] as $select)
                            <div><label for="{{ $select[0] }}"
                                    style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">{{ $select[1] }}</label><select
                                    name="{{ $select[0] }}" id="{{ $select[0] }}" required
                                    style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                                    <option value="" disabled selected>-- Pilih {{ $select[1] }} --</option>
                                    @foreach ($select[2] as $option)
                                        <option value="{{ $option->id }}">{{ $option->{$select[3]} }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                        <div style="grid-column:1/-1;"><label for="catatan"
                                style="display:block;color:#475569;font-weight:800;margin-bottom:.5rem;">Catatan</label>
                            <textarea name="catatan" id="catatan" rows="3"
                                style="width:100%;padding:.85rem;border:1px solid #E4E4D9;border-radius:.75rem;">{{ old('catatan') }}</textarea>
                        </div>
                    </div>
                    <div style="display:flex;gap:.75rem;margin-top:1.5rem;"><button type="submit"
                            style="background:#0B4F35;color:#fff;border:0;padding:.9rem 1.5rem;border-radius:.85rem;font-weight:800;">Simpan</button><a
                            href="{{ route('barang-masuk.index') }}"
                            style="background:#E4E4D9;color:#475569;padding:.9rem 1.5rem;border-radius:.85rem;text-decoration:none;font-weight:800;">Batal</a>
                    </div>
                </form>
        </div>
    </div>
    <script>
        function updateTotalHarga() {
            const q = parseFloat(document.querySelector('[name=jumlah]').value) || 0;
            const h = parseFloat(document.querySelector('[name=harga_satuan]').value) || 0;
            document.querySelector('[name=total_harga]').value = (q * h).toFixed(2)
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('[name=jumlah]').addEventListener('input', updateTotalHarga);
            document.querySelector('[name=harga_satuan]').addEventListener('input', updateTotalHarga)
        });

        function confirmSimpan() {
            return confirm('Apakah Anda yakin ingin menyimpan data barang masuk ini?')
        }
    </script>
@endsection

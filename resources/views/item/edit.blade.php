@extends('layouts.app')

@section('content')
    <div style="background:#FAF9F6; min-height:100vh; padding:2rem 1.25rem;">
        <div style="max-width:900px;margin:auto;">
            <div
                style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1.5rem;flex-wrap:wrap;">
                <div>
                    <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;letter-spacing:.08em;">
                        Master Data</p>
                    <h4 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;">Edit Item</h4>
                </div><a href="{{ route('item.index') }}"
                    style="color:#475569;background:#E4E4D9;padding:.75rem 1rem;border-radius:.75rem;text-decoration:none;font-weight:700;">Kembali</a>
            </div>
            @if ($errors->any())
                <div
                    style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c;padding:1rem;border-radius:1rem;margin-bottom:1rem;">
                    <ul style="margin:0;padding-left:1.25rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('item.update', $item->id) }}" method="POST" enctype="multipart/form-data"
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.05);">
                @csrf @method('PUT')<div
                    style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1.25rem;">
                    <div style="grid-column:1/-1;"><label for="nama_barang"
                            style="display:block;font-weight:800;color:#475569;margin-bottom:.5rem;">Nama Barang</label>
                        <div style="display:flex;gap:.5rem;"><input type="text" id="nama_barang" name="nama_barang"
                                value="{{ old('nama_barang', $item->nama_barang) }}" required
                                style="flex:1;padding:.85rem 1rem;border:1px solid #E4E4D9;border-radius:.75rem;"><button
                                type="button" onclick="speakField('nama_barang')"
                                style="border:0;background:#ecfdf5;color:#047857;border-radius:.75rem;padding:0 1rem;font-size:1.3rem;">🎙</button>
                        </div>
                    </div>
                    <div><label for="harga_dasar"
                            style="display:block;font-weight:800;color:#475569;margin-bottom:.5rem;">Harga
                            Dasar</label><input type="number" id="harga_dasar" name="harga_dasar"
                            value="{{ old('harga_dasar', $item->harga_dasar) }}" required
                            style="width:100%;padding:.85rem 1rem;border:1px solid #E4E4D9;border-radius:.75rem;"></div>
                    <div><label for="stok_minimum"
                            style="display:block;font-weight:800;color:#475569;margin-bottom:.5rem;">Stok
                            Minimum</label><input type="number" id="stok_minimum" name="stok_minimum"
                            value="{{ old('stok_minimum', $item->stok_minimum ?? '') }}" required
                            style="width:100%;padding:.85rem 1rem;border:1px solid #E4E4D9;border-radius:.75rem;"></div>
                    <div><label for="id_kategori"
                            style="display:block;font-weight:800;color:#475569;margin-bottom:.5rem;">Kategori</label><select
                            id="id_kategori" name="id_kategori" required
                            style="width:100%;padding:.85rem 1rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                            @foreach ($kategori as $k)
                                <option value="{{ $k->id }}"
                                    {{ old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' }}>
                                    {{ $k->kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div><label for="id_satuan"
                            style="display:block;font-weight:800;color:#475569;margin-bottom:.5rem;">Satuan</label><select
                            id="id_satuan" name="id_satuan" required
                            style="width:100%;padding:.85rem 1rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                            @foreach ($satuan as $s)
                                <option value="{{ $s->id }}"
                                    {{ old('id_satuan', $item->id_satuan) == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama_satuan }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="grid-column:1/-1;"><label for="deskripsi"
                            style="display:block;font-weight:800;color:#475569;margin-bottom:.5rem;">Catatan</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                            style="width:100%;padding:.85rem 1rem;border:1px solid #E4E4D9;border-radius:.75rem;">{{ old('deskripsi', $item->deskripsi) }}</textarea>
                    </div>
                    <div style="grid-column:1/-1;"><label for="foto"
                            style="display:block;font-weight:800;color:#475569;margin-bottom:.5rem;">Foto</label><input
                            type="file" id="foto" name="foto"
                            style="width:100%;padding:.75rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                        @if ($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto"
                                style="width:96px;height:96px;object-fit:cover;border-radius:1rem;margin-top:.75rem;">
                        @endif @error('foto')
                        <p style="color:#ef4444;font-size:.75rem;font-weight:600;">{{ $message }}</p>
                    @enderror
                </div>
            </div><button type="submit"
                style="margin-top:1.5rem;background:#0B4F35;color:#fff;border:0;padding:.9rem 1.5rem;border-radius:.85rem;font-weight:800;">Update
                Item</button></form>
    </div>
</div>
<script>
    function speakField(id) {
        if ('speechSynthesis' in window) {
            speechSynthesis.cancel();
            speechSynthesis.speak(new SpeechSynthesisUtterance('Isi ' + document.getElementById(id)
                .previousElementSibling.textContent));
        }
    }
</script>
@endsection
<style>
    input:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #0B4F35 !important;
        box-shadow: 0 0 0 3px rgba(11, 79, 53, .12)
    }
</style>

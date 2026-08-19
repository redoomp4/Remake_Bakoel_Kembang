@extends('layouts.app')

@section('content')

    <div class="min-h-screen bg-[#FAF9F6] px-4 py-6 md:px-6 md:py-8">

        <div class="max-w-4xl mx-auto">

            {{-- HEADER --}}
            <div class="mb-6">
                <p class="text-xs font-extrabold uppercase tracking-widest text-[#8FA882]">
                    Inventori · Barang Masuk
                </p>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-1">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-[#0B4F35]">
                            Tambah Barang Masuk
                        </h1>

                        <p class="text-sm text-[#475569] mt-1">
                            Catat penerimaan barang ke dalam inventori.
                        </p>
                    </div>

                    <a href="{{ route('barang-masuk.index') }}"
                        class="inline-flex items-center justify-center gap-2
                          px-4 py-2.5 rounded-xl
                          bg-white border border-[#E4E4D9]
                          text-sm font-bold text-[#475569]
                          hover:bg-[#FAF9F6] transition">
                        <i class="fas fa-arrow-left"></i>
                        Kembali
                    </a>
                </div>
            </div>


            {{-- ALERT ERROR --}}
            @if ($errors->any())
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-circle-exclamation mt-0.5"></i>

                        <div>
                            <p class="font-extrabold text-sm mb-1">
                                Terdapat kesalahan pada form
                            </p>

                            <ul class="list-disc ml-5 text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif


            {{-- SESSION ERROR --}}
            @if (session('error'))
                <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 text-red-700">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-circle-exclamation"></i>
                        <span class="font-bold text-sm">
                            {{ session('error') }}
                        </span>
                    </div>
                </div>
            @endif


            {{-- FORM --}}
            <form method="POST" action="{{ route('barang-masuk.store') }}" onsubmit="return confirmSimpan();"
                class="bg-white rounded-3xl border border-[#E4E4D9]
                     shadow-sm overflow-hidden">

                @csrf

                {{-- FORM HEADER --}}
                <div class="px-5 py-5 md:px-7 border-b border-[#E4E4D9] bg-[#FAF9F6]">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl bg-[#0B4F35]
                                text-white flex items-center justify-center">
                            <i class="fas fa-box-open"></i>
                        </div>

                        <div>
                            <h2 class="font-black text-[#0B4F35]">
                                Informasi Barang
                            </h2>

                            <p class="text-xs text-[#475569]">
                                Masukkan detail barang yang diterima.
                            </p>
                        </div>

                    </div>

                </div>


                {{-- FORM BODY --}}
                <div class="p-5 md:p-7 space-y-6">

                    {{-- ITEM --}}
                    <div>

                        <label for="item_id" class="block text-sm font-extrabold text-[#475569] mb-2">
                            Pilih Item
                        </label>

                        <div class="flex gap-2">

                            <select name="item_id" id="item_id" required
                                class="flex-1 px-4 py-3 rounded-xl
                   border-2 border-[#E4E4D9]
                   bg-white text-sm font-semibold
                   focus:outline-none
                   focus:border-[#0B4F35]
                   focus:ring-4 focus:ring-[#0B4F35]/10">

                                <option value="" disabled {{ old('item_id') ? '' : 'selected' }}>
                                    -- Pilih Item --
                                </option>

                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>

                                        {{ $item->kode_barang }}
                                        — {{ $item->nama_barang }}

                                    </option>
                                @endforeach

                            </select>


                        </div>

                        @error('item_id')
                            <p class="text-xs font-bold text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- JUMLAH + HARGA --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- JUMLAH --}}
                        <div>

                            <label for="jumlah" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Jumlah
                            </label>

                            <div class="relative">

                                <input type="number" id="jumlah" name="jumlah" min="1"
                                    value="{{ old('jumlah') }}" required placeholder="Contoh: 10"
                                    class="w-full px-4 py-3 rounded-xl
                                       border-2 border-[#E4E4D9]
                                       text-sm font-semibold
                                       focus:outline-none
                                       focus:border-[#0B4F35]
                                       focus:ring-4 focus:ring-[#0B4F35]/10">

                            </div>

                            @error('jumlah')
                                <p class="text-xs font-bold text-red-500 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- HARGA --}}
                        <div>

                            <label for="harga_satuan" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Harga Beli / Satuan
                            </label>

                            <div class="relative">


                                <input type="number" id="harga_satuan" name="harga_satuan" min="0" step="0.01"
                                    value="{{ old('harga_satuan') }}" required placeholder="0"
                                    class="w-full pl-12 pr-4 py-3 rounded-xl
                                       border-2 border-[#E4E4D9]
                                       text-sm font-semibold
                                       focus:outline-none
                                       focus:border-[#0B4F35]
                                       focus:ring-4 focus:ring-[#0B4F35]/10">

                            </div>

                            @error('harga_satuan')
                                <p class="text-xs font-bold text-red-500 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- TOTAL --}}
                    <div class="rounded-2xl bg-emerald-50
                            border border-emerald-100 p-4">

                        <div class="flex items-center justify-between gap-4">

                            <div>
                                <p class="text-xs font-black uppercase tracking-widest text-emerald-600">
                                    Total Nilai Barang
                                </p>

                                <p class="text-xs text-[#475569] mt-1">
                                    Jumlah × Harga Beli
                                </p>
                            </div>

                            <div class="text-right">

                                <p class="text-xs font-bold text-emerald-600">
                                    TOTAL
                                </p>

                                <p id="total_harga_display" class="text-xl md:text-2xl font-black text-[#0B4F35]">Rp 0</p>

                            </div>

                        </div>

                        <input type="hidden" id="total_harga" name="total_harga" value="{{ old('total_harga', 0) }}">

                    </div>


                    {{-- TANGGAL --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                        {{-- TANGGAL MASUK --}}
                        <div>

                            <label for="tanggal_masuk" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Tanggal Masuk
                            </label>

                            <input type="datetime-local" id="tanggal_masuk" name="tanggal_masuk"
                                value="{{ old('tanggal_masuk', now()->format('Y-m-d\TH:i')) }}" required
                                class="w-full px-4 py-3 rounded-xl
                                   border-2 border-[#E4E4D9]
                                   text-sm font-semibold
                                   focus:outline-none
                                   focus:border-[#0B4F35]">

                            @error('tanggal_masuk')
                                <p class="text-xs font-bold text-red-500 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- KADALUARSA --}}
                        <div>

                            <label for="tanggal_kadaluarsa" class="block text-sm font-extrabold text-[#475569] mb-2">

                                Tanggal Kadaluarsa

                                <span class="text-xs font-medium text-gray-400">
                                    (Opsional)
                                </span>

                            </label>

                            <input type="datetime-local" id="tanggal_kadaluarsa" name="tanggal_kadaluarsa"
                                value="{{ old('tanggal_kadaluarsa') }}"
                                class="w-full px-4 py-3 rounded-xl
                                   border-2 border-[#E4E4D9]
                                   text-sm font-semibold
                                   focus:outline-none
                                   focus:border-[#0B4F35]">

                            @error('tanggal_kadaluarsa')
                                <p class="text-xs font-bold text-red-500 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>


                    {{-- PEMASOK / LOKASI / KONDISI --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                        {{-- PEMASOK --}}
                        <div>

                            <label for="id_pemasok" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Pemasok
                            </label>

                            <select name="id_pemasok" id="id_pemasok" required
                                class="w-full px-4 py-3 rounded-xl
                                   border-2 border-[#E4E4D9]
                                   text-sm font-semibold
                                   focus:outline-none
                                   focus:border-[#0B4F35]">

                                <option value="" disabled {{ old('id_pemasok') ? '' : 'selected' }}>
                                    Pilih Pemasok
                                </option>

                                @foreach ($pemasoks as $pemasok)
                                    <option value="{{ $pemasok->id }}"
                                        {{ old('id_pemasok') == $pemasok->id ? 'selected' : '' }}>

                                        {{ $pemasok->nama_pemasok }}

                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- LOKASI --}}
                        <div>

                            <label for="id_lokasi" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Lokasi
                            </label>

                            <select name="id_lokasi" id="id_lokasi" required
                                class="w-full px-4 py-3 rounded-xl
                                   border-2 border-[#E4E4D9]
                                   text-sm font-semibold
                                   focus:outline-none
                                   focus:border-[#0B4F35]">

                                <option value="" disabled {{ old('id_lokasi') ? '' : 'selected' }}>
                                    Pilih Lokasi
                                </option>

                                @foreach ($lokasis as $lokasi)
                                    <option value="{{ $lokasi->id }}"
                                        {{ old('id_lokasi') == $lokasi->id ? 'selected' : '' }}>

                                        {{ $lokasi->nama_lokasi }}

                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- KONDISI --}}
                        <div>

                            <label for="id_kondisi" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Kondisi
                            </label>

                            <select name="id_kondisi" id="id_kondisi" required
                                class="w-full px-4 py-3 rounded-xl
                                   border-2 border-[#E4E4D9]
                                   text-sm font-semibold
                                   focus:outline-none
                                   focus:border-[#0B4F35]">

                                <option value="" disabled {{ old('id_kondisi') ? '' : 'selected' }}>
                                    Pilih Kondisi
                                </option>

                                @foreach ($kondisis as $kondisi)
                                    <option value="{{ $kondisi->id }}"
                                        {{ old('id_kondisi') == $kondisi->id ? 'selected' : '' }}>

                                        {{ $kondisi->nama_kondisi }}

                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>


                    {{-- CATATAN --}}
                    <div>

                        <label for="catatan" class="block text-sm font-extrabold text-[#475569] mb-2">
                            Catatan
                        </label>

                        <textarea name="catatan" id="catatan" rows="3" maxlength="1000"
                            placeholder="Tambahkan catatan jika diperlukan..."
                            class="w-full px-4 py-3 rounded-xl
                               border-2 border-[#E4E4D9]
                               text-sm
                               focus:outline-none
                               focus:border-[#0B4F35]
                               focus:ring-4 focus:ring-[#0B4F35]/10">{{ old('catatan') }}</textarea>

                        @error('catatan')
                            <p class="text-xs font-bold text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>


                {{-- FORM FOOTER --}}
                <div
                    class="px-5 py-5 md:px-7
                        bg-[#FAF9F6]
                        border-t border-[#E4E4D9]
                        flex flex-col-reverse sm:flex-row
                        sm:justify-end gap-3">

                    <a href="{{ route('barang-masuk.index') }}"
                        class="inline-flex items-center justify-center gap-2
                          px-5 py-3 rounded-xl
                          bg-white border border-[#E4E4D9]
                          text-sm font-extrabold text-[#475569]
                          hover:bg-gray-50 transition">

                        <i class="fas fa-xmark"></i>
                        Batal

                    </a>

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2
                           px-6 py-3 rounded-xl
                           bg-[#0B4F35] text-white
                           text-sm font-extrabold
                           shadow-sm hover:bg-[#083d29]
                           transition">

                        <i class="fas fa-save"></i>
                        Simpan Barang Masuk

                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- JAVASCRIPT --}}
    <script>
        function updateTotalHarga() {

            const jumlah =
                parseFloat(document.getElementById('jumlah').value) || 0;

            const harga =
                parseFloat(document.getElementById('harga_satuan').value) || 0;

            const total = jumlah * harga;

            document.getElementById('total_harga').value = total;

            document.getElementById('total_harga_display').textContent =
                'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }


        document.addEventListener('DOMContentLoaded', function() {

            const jumlah = document.getElementById('jumlah');
            const harga = document.getElementById('harga_satuan');

            jumlah.addEventListener('input', updateTotalHarga);
            harga.addEventListener('input', updateTotalHarga);

            updateTotalHarga();

        });


        function confirmSimpan() {

            return confirm(
                'Apakah Anda yakin ingin menyimpan data barang masuk ini?'
            );

        }
    </script>

@endsection

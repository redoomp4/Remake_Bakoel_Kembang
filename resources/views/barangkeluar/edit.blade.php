@extends('layouts.app')

@section('content')

    <div class="min-h-screen bg-[#FAF9F6] px-4 py-6 md:px-6 md:py-8">
        <div class="max-w-4xl mx-auto">
            {{-- HEADER --}}
            <div class="mb-6">
                <p class="text-xs font-extrabold uppercase tracking-widest text-[#8FA882]">
                    Inventori · Arus Keluar
                </p>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-1">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-black text-[#0B4F35]">
                            Edit Barang Keluar
                        </h1>
                        <p class="text-sm text-[#475569] mt-1">
                            Perbarui informasi transaksi barang keluar.
                        </p>
                    </div>
                    <a href="{{ route('barang-keluar.index') }}"
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
            {{-- ALERT VALIDATION --}}
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
            <form method="POST" action="{{ route('barang-keluar.update', $barangKeluar->id) }}"
                onsubmit="return confirmUpdate();"
                class="bg-white rounded-3xl border border-[#E4E4D9]
            shadow-sm overflow-hidden">
                @csrf
                @method('PUT')
                {{-- FORM HEADER --}}
                <div class="px-5 py-5 md:px-7
                border-b border-[#E4E4D9]
                bg-[#FAF9F6]">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-[#0B4F35]
                        text-white flex items-center justify-center">
                            <i class="fas fa-pen"></i>
                        </div>
                        <div>
                            <h2 class="font-black text-[#0B4F35]">
                                Informasi Barang Keluar
                            </h2>
                            <p class="text-xs text-[#475569]">
                                Perbarui detail transaksi barang yang keluar.
                            </p>
                        </div>
                    </div>
                </div>
                {{-- FORM BODY --}}
                <div class="p-5 md:p-7 space-y-6">
                    {{-- BARANG --}}
                    <div>
                        <label for="kode_lokasi_kondisi" class="block text-sm font-extrabold text-[#475569] mb-2">
                            Pilih Barang *
                        </label>
                        <div class="flex gap-2">
                            <select name="kode_lokasi_kondisi" id="kode_lokasi_kondisi" required
                                class="flex-1 px-4 py-3 rounded-xl
                            border-2 border-[#E4E4D9]
                            bg-white text-sm font-semibold
                            focus:outline-none
                            focus:border-[#0B4F35]
                            focus:ring-4 focus:ring-[#0B4F35]/10">
                                <option value="">
                                    -- Memuat data barang --
                                </option>
                            </select>
                            <button type="button"
                                onclick="alert('Fitur scan dapat dikembangkan menggunakan kamera perangkat.')"
                                class="px-4 rounded-xl
                            bg-emerald-50
                            border border-emerald-200
                            text-emerald-700
                            font-extrabold text-sm
                            hover:bg-emerald-100 transition">
                                <i class="fas fa-camera mr-1"></i>
                                <span class="hidden sm:inline">
                                    Scan
                                </span>
                            </button>
                        </div>
                        @error('kode_lokasi_kondisi')
                            <p class="text-xs font-bold text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    {{-- HIDDEN --}}
                    <input type="hidden" name="id_lokasi" id="id_lokasi"
                        value="{{ old('id_lokasi', $barangKeluar->id_lokasi) }}">
                    <input type="hidden" name="id_kondisi" id="id_kondisi"
                        value="{{ old('id_kondisi', $barangKeluar->id_kondisi) }}">
                    <input type="hidden" name="item_id" id="item_id"
                        value="{{ old('item_id', $barangKeluar->item_id) }}">
                    {{-- INFORMASI BARANG --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach ([['nama_barang', 'Nama Barang'], ['satuan', 'Satuan'], ['stok_tersedia', 'Stok Tersedia'], ['harga_dasar', 'Harga Rata-Rata']] as $field)
                            <div>
                                <label for="{{ $field[0] }}" class="block text-sm font-extrabold text-[#475569] mb-2">
                                    {{ $field[1] }}
                                </label>
                                <input type="text" id="{{ $field[0] }}" readonly
                                    class="w-full px-4 py-3 rounded-xl
                                border-2 border-[#E4E4D9]
                                bg-gray-50
                                text-sm font-semibold
                                text-gray-600">
                            </div>
                        @endforeach
                    </div>
                    {{-- JUMLAH + HARGA --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- JUMLAH --}}
                        <div>
                            <label for="jumlah_keluar" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Jumlah Keluar
                            </label>
                            <input type="number" name="jumlah_keluar" id="jumlah_keluar" min="1" required
                                value="{{ old('jumlah_keluar', $barangKeluar->jumlah_keluar) }}" placeholder="Contoh: 5"
                                class="w-full px-4 py-3 rounded-xl
                            border-2 border-[#E4E4D9]
                            text-sm font-semibold
                            focus:outline-none
                            focus:border-[#0B4F35]
                            focus:ring-4 focus:ring-[#0B4F35]/10">
                            <p id="jumlah_warning" class="text-xs font-bold text-red-500 mt-1">
                            </p>
                        </div>
                        {{-- HARGA JUAL --}}
                        <div>
                            <label for="harga_jual" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Harga Jual / Unit
                            </label>
                            <input type="number" name="harga_jual" id="harga_jual" min="0" step="0.01" required
                                value="{{ old('harga_jual', $barangKeluar->harga_jual) }}" placeholder="0"
                                class="w-full px-4 py-3 rounded-xl
                            border-2 border-[#E4E4D9]
                            text-sm font-semibold
                            focus:outline-none
                            focus:border-[#0B4F35]
                            focus:ring-4 focus:ring-[#0B4F35]/10">
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
                                    Jumlah × Harga Jual
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-bold text-emerald-600">
                                    TOTAL
                                </p>
                                <p id="total_harga_display" class="text-xl md:text-2xl font-black text-[#0B4F35]">
                                    Rp 0
                                </p>
                            </div>
                        </div>
                        <input type="hidden" id="total_harga_jual" name="total_harga_jual"
                            value="{{ old('total_harga_jual', $barangKeluar->total_harga_jual) }}">
                    </div>
                    {{-- PENERIMA + JENIS TRANSAKSI --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- PENERIMA --}}
                        <div>
                            <label for="penerima" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Penerima
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="penerima" id="penerima"
                                    value="{{ old('penerima', $barangKeluar->penerima) }}" required
                                    placeholder="Nama penerima"
                                    class="flex-1 px-4 py-3 rounded-xl
                                border-2 border-[#E4E4D9]
                                text-sm font-semibold
                                focus:outline-none
                                focus:border-[#0B4F35]
                                focus:ring-4 focus:ring-[#0B4F35]/10">
                                <button type="button" onclick="speakField('penerima')"
                                    class="px-4 rounded-xl
                                bg-emerald-50
                                border border-emerald-200
                                text-emerald-700
                                hover:bg-emerald-100">
                                    <i class="fas fa-microphone"></i>
                                </button>
                            </div>
                            @error('penerima')
                                <p class="text-xs font-bold text-red-500 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- JENIS TRANSAKSI --}}
                        <div>
                            <label for="jenis_transaksi" class="block text-sm font-extrabold text-[#475569] mb-2">
                                Jenis Transaksi
                            </label>
                            <select name="jenis_transaksi" id="jenis_transaksi" required
                                class="w-full px-4 py-3 rounded-xl
                            border-2 border-[#E4E4D9]
                            bg-white
                            text-sm font-semibold
                            focus:outline-none
                            focus:border-[#0B4F35]
                            focus:ring-4 focus:ring-[#0B4F35]/10">
                                <option value="" disabled>
                                    -- Pilih Jenis Transaksi --
                                </option>
                                @foreach (['Penjualan', 'Donasi', 'Pemakaian Internal', 'Pemindahan Barang', 'Retur ke Supplier', 'Penghapusan', 'Lainnya'] as $jenis)
                                    <option value="{{ $jenis }}"
                                        {{ old('jenis_transaksi', $barangKeluar->jenis_transaksi) == $jenis ? 'selected' : '' }}>
                                        {{ $jenis }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_transaksi')
                                <p class="text-xs font-bold text-red-500 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    {{-- LOKASI TUJUAN --}}
                    <div>
                        <label for="lokasi_tujuan" class="block text-sm font-extrabold text-[#475569] mb-2">
                            Lokasi Tujuan
                        </label>
                        <input type="text" name="lokasi_tujuan" id="lokasi_tujuan"
                            value="{{ old('lokasi_tujuan', $barangKeluar->lokasi_tujuan) }}" required
                            placeholder="Contoh: Gudang A / Toko Cabang / Rumah Pelanggan"
                            class="w-full px-4 py-3 rounded-xl
                        border-2 border-[#E4E4D9]
                        text-sm font-semibold
                        focus:outline-none
                        focus:border-[#0B4F35]
                        focus:ring-4 focus:ring-[#0B4F35]/10">
                        @error('lokasi_tujuan')
                            <p class="text-xs font-bold text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    {{-- CATATAN --}}
                    <div>
                        <label for="catatan" class="block text-sm font-extrabold text-[#475569] mb-2">
                            Catatan
                        </label>
                        <textarea name="catatan" id="catatan" rows="3" maxlength="1000"
                            placeholder="Contoh: Penerimaan Penjualan, Penghapusan, Retur..."
                            class="w-full px-4 py-3 rounded-xl
                        border-2 border-[#E4E4D9]
                        text-sm
                        focus:outline-none
                        focus:border-[#0B4F35]
                        focus:ring-4 focus:ring-[#0B4F35]/10">{{ old('catatan', $barangKeluar->catatan) }}</textarea>
                        @error('catatan')
                            <p class="text-xs font-bold text-red-500 mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
                {{-- FOOTER --}}
                <div
                    class="px-5 py-5 md:px-7
                bg-[#FAF9F6]
                border-t border-[#E4E4D9]
                flex flex-col-reverse sm:flex-row
                sm:justify-end gap-3">
                    <a href="{{ route('barang-keluar.index') }}"
                        class="inline-flex items-center justify-center gap-2
                    px-5 py-3 rounded-xl
                    bg-white border border-[#E4E4D9]
                    text-sm font-extrabold text-[#475569]
                    hover:bg-gray-50 transition">
                        <i class="fas fa-xmark"></i>
                        Batal
                    </a>
                    <button type="submit" id="submitBtn"
                        class="inline-flex items-center justify-center gap-2
                    px-6 py-3 rounded-xl
                    bg-[#0B4F35] text-white
                    text-sm font-extrabold
                    shadow-sm hover:bg-[#083d29]
                    transition">
                        <i class="fas fa-save"></i>
                        Update Barang Keluar
                    </button>
                </div>
            </form>

        </div>
    </div>


    {{-- JAVASCRIPT --}}
    <script>
        /*
            |--------------------------------------------------------------------------
            | Helper angka
            |--------------------------------------------------------------------------
            */

        function num(value) {
            const number = parseFloat(
                String(value ?? '').replace(/[^\d.-]/g, '')
            );
            return isNaN(number) ? 0 : number;
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil harga dasar
        |--------------------------------------------------------------------------
        */

        function pickHarga(data) {
            return num(
                data?.harga_dasar ??
                data?.harga ??
                data?.harga_satuan ??
                data?.item?.harga_dasar
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Text to Speech
        |--------------------------------------------------------------------------
        */

        function speakField(id) {
            if ('speechSynthesis' in window) {
                speechSynthesis.speak(
                    new SpeechSynthesisUtterance(
                        'Masukkan ' + id
                    )
                );
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Load daftar barang
        |--------------------------------------------------------------------------
        */

        async function loadPilihanBarang() {
            try {
                const response = await fetch(
                    "{{ route('barang-keluar.pilihan-barang') }}"
                );
                if (!response.ok) {
                    throw new Error(
                        'Gagal mengambil data barang.'
                    );
                }
                const data = await response.json();
                const select =
                    document.getElementById(
                        'kode_lokasi_kondisi'
                    );
                select.innerHTML = '';
                /*
                | Option default
                */
                const defaultOption =
                    document.createElement('option');
                defaultOption.value = '';
                defaultOption.text =
                    '-- Pilih Barang --';
                defaultOption.disabled = true;
                select.appendChild(defaultOption);
                /*
                | Data barang
                */
                data.forEach(item => {
                    const option =
                        document.createElement('option');
                    option.dataset.itemId =
                        item.item_id;
                    option.value =
                        `${item.kode}|${item.lokasi_id}|${item.kondisi_id}`;
                    option.text =
                        `${item.kode} - ${item.nama_barang} - ${item.lokasi} - ${item.kondisi} (Stok: ${item.stok})`;
                    option.dataset.nama =
                        item.nama_barang ?? '';
                    option.dataset.satuan =
                        item.satuan ?? '';
                    option.dataset.stok =
                        item.stok ?? 0;
                    option.dataset.harga =
                        pickHarga(item);
                    select.appendChild(option);
                });
                /*
                | Pilih data lama
                */
                const oldItemId =
                    "{{ old('item_id', $barangKeluar->item_id) }}";
                const oldLokasi =
                    "{{ old('id_lokasi', $barangKeluar->id_lokasi) }}";
                const oldKondisi =
                    "{{ old('id_kondisi', $barangKeluar->id_kondisi) }}";
                const targetValue =
                    `${getItemKode()}|${oldLokasi}|${oldKondisi}`;
                let found = false;
                /*
                | Cari berdasarkan item + lokasi + kondisi
                */
                Array.from(select.options).forEach(option => {
                    if (
                        option.dataset.itemId == oldItemId &&
                        option.value.split('|')[1] == oldLokasi &&
                        option.value.split('|')[2] == oldKondisi
                    ) {
                        option.selected = true;
                        found = true;
                        setBarangFromOption(option);
                    }
                });
                /*
                | Jika ditemukan
                */
                if (found) {
                    const selected =
                        select.options[
                            select.selectedIndex
                        ];
                    await fetchDetail(
                        selected.dataset.itemId,
                        oldLokasi,
                        oldKondisi
                    );
                }
                /*
                | Jika tidak ditemukan
                */
                else {
                    select.value = '';
                }
                updateTotal();
            } catch (error) {
                console.error(error);
                alert(
                    'Gagal mengambil daftar barang. Silakan refresh halaman.'
                );
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Ambil kode item
        |--------------------------------------------------------------------------
        |
        | Karena value pilihan barang menggunakan kode barang,
        | kita ambil kode dari relasi item.
        |
        */

        function getItemKode() {
            return @json($barangKeluar->item->kode_barang ?? ($barangKeluar->kode_barang ?? ''));

        }


        /*
        |--------------------------------------------------------------------------
        | Isi informasi barang dari option
        |--------------------------------------------------------------------------
        */

        function setBarangFromOption(option) {
            if (!option) {
                return;
            }
            document.getElementById(
                    'item_id'
                ).value =
                option.dataset.itemId ?? '';
            const parts =
                option.value.split('|');
            document.getElementById(
                    'id_lokasi'
                ).value =
                parts[1] ?? '';
            document.getElementById(
                    'id_kondisi'
                ).value =
                parts[2] ?? '';
            document.getElementById(
                    'nama_barang'
                ).value =
                option.dataset.nama ?? '';
            document.getElementById(
                    'satuan'
                ).value =
                option.dataset.satuan ?? '';
            document.getElementById(
                    'stok_tersedia'
                ).value =
                num(option.dataset.stok);
            document.getElementById(
                    'harga_dasar'
                ).value =
                num(option.dataset.harga);

        }


        /*
        |--------------------------------------------------------------------------
        | Fetch detail barang
        |--------------------------------------------------------------------------
        */

        async function fetchDetail(
            itemId,
            lokasi,
            kondisi
        ) {
            try {
                const response = await fetch(
                    `{{ route('barang-keluar.detail-barang') }}?item_id=${encodeURIComponent(itemId)}&id_lokasi=${encodeURIComponent(lokasi)}&id_kondisi=${encodeURIComponent(kondisi)}`
                );
                if (!response.ok) {
                    throw new Error(
                        'Gagal mengambil detail barang.'
                    );
                }
                const data =
                    await response.json();
                document.getElementById(
                        'nama_barang'
                    ).value =
                    data.nama_barang ?? '';
                document.getElementById(
                        'satuan'
                    ).value =
                    data.satuan ?? '';
                /*
                    | Stok dari endpoint
                    */
                document.getElementById(
                        'stok_tersedia'
                    ).value =
                    num(data.stok);
                document.getElementById(
                        'harga_dasar'
                    ).value =
                    pickHarga(data);
            } catch (error) {
                console.error(error);
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Update total
        |--------------------------------------------------------------------------
        */

        function updateTotal() {
            const jumlah =
                num(
                    document.getElementById(
                        'jumlah_keluar'
                    ).value
                );
            const harga =
                num(
                    document.getElementById(
                        'harga_jual'
                    ).value
                );
            const stok =
                num(
                    document.getElementById(
                        'stok_tersedia'
                    ).value
                );
            const total =
                jumlah * harga;
            document.getElementById(
                    'total_harga_jual'
                ).value =
                total;
            document.getElementById(
                    'total_harga_display'
                ).textContent =
                'Rp ' +
                new Intl.NumberFormat(
                    'id-ID'
                ).format(total);
            const warning =
                document.getElementById(
                    'jumlah_warning'
                );
            const submit =
                document.getElementById(
                    'submitBtn'
                );
            /*
            |--------------------------------------------------------------------------
            | Validasi jumlah
            |--------------------------------------------------------------------------
            */
           if (jumlah > stok) {
                warning.textContent =
                    `Jumlah melebihi stok tersedia (${stok}).`;
                submit.disabled = true;
                submit.classList.add(
                    'opacity-50',
                    'cursor-not-allowed'
                );
            } else if (jumlah < 1) {
                warning.textContent =
                    'Jumlah minimal 1.';
                submit.disabled = true;
                submit.classList.add(
                    'opacity-50',
                    'cursor-not-allowed'
                );
            } else {
                warning.textContent = '';
                submit.disabled = false;
                submit.classList.remove(
                    'opacity-50',
                    'cursor-not-allowed'
                );
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Confirm update
        |--------------------------------------------------------------------------
        */

        function confirmUpdate() {
            const jumlah =
                num(
                    document.getElementById(
                        'jumlah_keluar'
                    ).value
                );
            const stok =
                num(
                    document.getElementById(
                        'stok_tersedia'
                    ).value
                );
            if (jumlah > stok) {
                alert(
                    `Jumlah keluar melebihi stok tersedia. Sisa stok: ${stok}`
                );
                return false;
            }
            return confirm(
                'Apakah Anda yakin ingin memperbarui transaksi barang keluar ini?'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | DOM READY
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {
                const select =
                    document.getElementById(
                        'kode_lokasi_kondisi'
                    );
                const jumlah =
                    document.getElementById(
                        'jumlah_keluar'
                    );
                const harga =
                    document.getElementById(
                        'harga_jual'
                    );
                /*
                    | Load barang
                    */
                loadPilihanBarang();
                /*
                | Ketika barang berubah
                */
                select.addEventListener(
                    'change',
                    async function() {
                        if (!this.value) {
                            return;
                        }
                        const [
                            kode,
                            lokasi,
                            kondisi
                        ] =
                        this.value.split('|');
                        const option =
                            this.options[
                                this.selectedIndex
                            ];
                        setBarangFromOption(option);
                        await fetchDetail(
                            option.dataset.itemId,
                            lokasi,
                            kondisi
                        );
                        updateTotal();
                    }
                );
                /*
                | Jumlah berubah
                */
                jumlah.addEventListener(
                    'input',
                    updateTotal
                );
                /*
                | Harga berubah
                */
                harga.addEventListener(
                    'input',
                    updateTotal
                );
                /*
                | Total awal
                */
                updateTotal();
            }
        );
    </script>

@endsection

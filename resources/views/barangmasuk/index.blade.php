@extends('layouts.app')

@section('content')
    <div
        style="
        background:#FAF9F6;
        min-height:100vh;
        width:100%;
        padding:2rem 1.25rem 3rem;
        box-sizing:border-box;
        overflow-x:hidden;
    ">

        <div
            style="
            width:100%;
            max-width:1250px;
            margin:0 auto;
            box-sizing:border-box;
        ">

            {{-- HEADER --}}
            <div
                style="
                display:flex;
                justify-content:space-between;
                align-items:center;
                gap:1rem;
                flex-wrap:wrap;
                margin-bottom:1.5rem;
            ">

                <div style="min-width:0;">
                    <p
                        style="
                        color:#8FA882;
                        font-weight:800;
                        font-size:.75rem;
                        text-transform:uppercase;
                        margin:0 0 .35rem 0;
                    ">
                        Inventori · Arus Masuk
                    </p>

                    <h4
                        style="
                        color:#0B4F35;
                        font-size:1.9rem;
                        font-weight:900;
                        margin:0;
                    ">
                        Daftar Barang Masuk
                    </h4>
                </div>

                <a href="{{ route('barang-masuk.create') }}"
                    style="
                        background:#0B4F35;
                        color:#fff;
                        padding:.8rem 1.25rem;
                        border-radius:.85rem;
                        text-decoration:none;
                        font-weight:800;
                        white-space:nowrap;
                    ">
                    <i class="fas fa-plus"></i>
                    Tambah Barang Masuk
                </a>

            </div>


            {{-- FILTER --}}
            <form method="GET" action="{{ route('barang-masuk.index') }}"
                style="
                    width:100%;
                    box-sizing:border-box;
                    background:#fff;
                    border:1px solid #E4E4D9;
                    border-radius:1.25rem;
                    padding:1.25rem;
                    display:flex;
                    gap:1rem;
                    align-items:flex-end;
                    flex-wrap:wrap;
                    margin-bottom:1.5rem;
                ">

                {{-- SEARCH --}}
                <div style="
                    flex:1 1 280px;
                    min-width:0;
                ">
                    <label for="search"
                        style="
                            display:block;
                            color:#475569;
                            font-weight:800;
                            font-size:.85rem;
                            margin-bottom:.5rem;
                        ">
                        Cari Barang
                    </label>

                    <input type="text" id="search" name="search" placeholder="Cari nama barang"
                        value="{{ request('search') }}"
                        style="
                            width:100%;
                            box-sizing:border-box;
                            padding:.8rem;
                            border:1px solid #E4E4D9;
                            border-radius:.75rem;
                        ">
                </div>


                {{-- LOKASI --}}
                <div style="
                    flex:1 1 280px;
                    min-width:0;
                ">
                    <label for="lokasi"
                        style="
                            display:block;
                            color:#475569;
                            font-weight:800;
                            font-size:.85rem;
                            margin-bottom:.5rem;
                        ">
                        Lokasi
                    </label>

                    <select name="lokasi" id="lokasi"
                        style="
                            width:100%;
                            box-sizing:border-box;
                            padding:.8rem;
                            border:1px solid #E4E4D9;
                            border-radius:.75rem;
                            background:#fff;
                        ">

                        <option value="">-- Semua Lokasi --</option>

                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama_lokasi }}
                            </option>
                        @endforeach

                    </select>
                </div>


                {{-- BUTTON --}}
                <div
                    style="
                    display:flex;
                    gap:.75rem;
                    flex-shrink:0;
                ">

                    <button type="submit"
                        style="
                            background:#0B4F35;
                            color:#fff;
                            border:0;
                            border-radius:.75rem;
                            padding:.8rem 1.25rem;
                            font-weight:800;
                            cursor:pointer;
                            white-space:nowrap;
                        ">
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>

                    <a href="{{ route('barang-masuk.index') }}"
                        style="
                            background:#E4E4D9;
                            color:#475569;
                            border-radius:.75rem;
                            padding:.8rem 1.25rem;
                            text-decoration:none;
                            font-weight:800;
                            white-space:nowrap;
                        ">
                        Reset
                    </a>

                </div>

            </form>


            {{-- TABLE WRAPPER --}}
            <div
                style="
                width:100%;
                max-width:100%;
                box-sizing:border-box;
                background:#fff;
                border:1px solid #E4E4D9;
                border-radius:1.5rem;
                box-shadow:0 1px 4px rgba(0,0,0,.05);
                overflow-x:auto;
                overflow-y:hidden;
            ">

                <table
                    style="
                    width:100%;
                    min-width:1050px;
                    border-collapse:collapse;
                    margin:0;
                ">

                    <thead
                        style="
                        background:#f0ebe3;
                        color:#475569;
                        font-size:.68rem;
                        text-transform:uppercase;
                        letter-spacing:.05em;
                    ">
                        <tr>

                            <th style="padding:.75rem;text-align:center;white-space:nowrap;">
                                No
                            </th>

                            <th style="padding:.75rem;text-align:left;white-space:nowrap;">
                                Tanggal Masuk
                            </th>

                            <th style="padding:.75rem;text-align:left;white-space:nowrap;">
                                Kode Barang
                            </th>

                            <th style="padding:.75rem;text-align:left;">
                                Nama Barang
                            </th>

                            <th style="padding:.75rem;text-align:center;white-space:nowrap;">
                                Jumlah
                            </th>

                            <th style="padding:.75rem;text-align:left;white-space:nowrap;">
                                Total Harga
                            </th>

                            <th style="padding:.75rem;text-align:left;">
                                Pemasok
                            </th>

                            <th style="padding:.75rem;text-align:left;">
                                Lokasi
                            </th>

                            <th style="padding:.75rem;text-align:left;">
                                Kondisi
                            </th>

                            <th style="padding:.75rem;text-align:center;white-space:nowrap;">
                                Aksi
                            </th>

                        </tr>
                    </thead>


                    <tbody>

                        @forelse($barangMasuks as $bm)
                            <tr style="
                                border-bottom:1px solid #E4E4D9;
                                font-size:.82rem;
                            "
                                onmouseover="this.style.background='#fafbf8'" onmouseout="this.style.background='#fff'">

                                {{-- NO --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    text-align:center;
                                    color:#64748b;
                                    font-weight:700;
                                ">
                                    {{ $barangMasuks->firstItem() + $loop->index }}
                                </td>


                                {{-- TANGGAL --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    white-space:nowrap;
                                    color:#475569;
                                ">
                                    {{ $bm->tanggal_masuk ? \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d-m-Y H:i') : '-' }}
                                </td>


                                {{-- KODE --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    font-weight:800;
                                    white-space:nowrap;
                                    color:#0B4F35;
                                ">
                                    {{ $bm->item->kode_barang ?? '-' }}
                                </td>


                                {{-- NAMA --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    max-width:180px;
                                    word-break:break-word;
                                ">
                                    {{ $bm->item->nama_barang ?? '-' }}
                                </td>


                                {{-- JUMLAH --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    text-align:center;
                                    white-space:nowrap;
                                ">
                                    {{ number_format($bm->jumlah, 0, ',', '.') }}
                                </td>


                                {{-- TOTAL HARGA --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    font-weight:800;
                                    white-space:nowrap;
                                ">
                                    Rp {{ number_format($bm->total_harga, 0, ',', '.') }}
                                </td>


                                {{-- PEMASOK --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    max-width:160px;
                                    word-break:break-word;
                                ">
                                    {{ $bm->pemasok->nama_pemasok ?? '-' }}
                                </td>


                                {{-- LOKASI --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    max-width:140px;
                                    word-break:break-word;
                                ">
                                    {{ $bm->lokasi->nama_lokasi ?? '-' }}
                                </td>


                                {{-- KONDISI --}}
                                <td style="padding:.75rem;">

                                    <span
                                        style="
                                        display:inline-block;
                                        background:#ecfdf5;
                                        color:#047857;
                                        padding:.35rem .6rem;
                                        border-radius:999px;
                                        font-size:.68rem;
                                        font-weight:800;
                                        white-space:nowrap;
                                    ">
                                        {{ $bm->kondisi->nama_kondisi ?? '-' }}
                                    </span>

                                </td>


                                {{-- AKSI --}}
                                <td
                                    style="
                                    padding:.75rem;
                                    white-space:nowrap;
                                ">

                                    <div
                                        style="
                                        display:flex;
                                        gap:.35rem;
                                        align-items:center;
                                        justify-content:center;
                                    ">

                                        {{-- DETAIL --}}
                                        <a href="{{ route('barang-masuk.detail', $bm->id) }}" title="Lihat Detail"
                                            style="
                                                background:#60a5fa;
                                                color:#fff;
                                                width:32px;
                                                height:32px;
                                                min-width:32px;
                                                border-radius:.55rem;
                                                text-decoration:none;
                                                display:inline-flex;
                                                align-items:center;
                                                justify-content:center;
                                            ">
                                            <i class="fas fa-eye" style="font-size:.8rem;"></i>
                                        </a>


                                        {{-- EDIT --}}
                                        <a href="{{ route('barang-masuk.edit', $bm->id) }}" title="Edit"
                                            style="
                                                background:#fbbf24;
                                                color:#1a1a1a;
                                                width:32px;
                                                height:32px;
                                                min-width:32px;
                                                border-radius:.55rem;
                                                text-decoration:none;
                                                display:inline-flex;
                                                align-items:center;
                                                justify-content:center;
                                            ">
                                            <i class="fas fa-pen" style="font-size:.8rem;"></i>
                                        </a>


                                        {{-- HAPUS --}}
                                        <form id="delete-form-{{ $bm->id }}"
                                            action="{{ route('barang-masuk.destroy', $bm->id) }}" method="POST"
                                            style="
                                                display:inline-flex;
                                                width:32px;
                                                min-width:32px;
                                                height:32px;
                                                margin:0;
                                                padding:0;
                                            ">

                                            @csrf
                                            @method('DELETE')

                                            <button type="button" title="Hapus"
                                                onclick="openDeleteModal({{ $bm->id }})"
                                                style="
                                                    background:#dc2626;
                                                    color:#fff;
                                                    width:32px;
                                                    height:32px;
                                                    min-width:32px;
                                                    padding:0;
                                                    margin:0;
                                                    border:0;
                                                    border-radius:.55rem;
                                                    cursor:pointer;
                                                    display:inline-flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                ">
                                                <i class="fas fa-trash" style="font-size:.8rem;"></i>
                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10"
                                    style="
                                        padding:3rem 1rem;
                                        text-align:center;
                                        color:#999;
                                    ">

                                    <i class="fas fa-box-open"
                                        style="
                                            font-size:1.8rem;
                                            margin-bottom:.75rem;
                                        ">
                                    </i>

                                    <div style="font-weight:700;">
                                        Tidak ada data Barang Masuk
                                    </div>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}
            <div
                style="
                width:100%;
                margin-top:1.5rem;
                text-align:center;
            ">
                {{ $barangMasuks->appends(request()->query())->links() }}
            </div>

        </div>

    </div>


    {{-- MODAL CONFIRM DELETE --}}
    <div id="delete-modal"
        class="fixed inset-0 bg-brand-emerald/40 backdrop-blur-sm
               flex items-center justify-center z-[999] p-4 hidden"
        role="dialog" aria-modal="true">

        <div id="delete-modal-content"
            class="bg-white text-gray-900
                   border-2 border-brand-emerald
                   p-7 md:p-10 rounded-3xl shadow-2xl
                   max-w-xl w-full text-center space-y-5
                   transform scale-95 transition-transform duration-300">

            <div
                class="w-16 h-16 bg-rose-50 rounded-2xl
                        flex items-center justify-center
                        text-rose-600 mx-auto">

                <i class="fas fa-trash-can text-3xl"></i>

            </div>

            <div>
                <h3 class="text-xl md:text-2xl font-black text-[#0B4F35]">
                    Hapus Barang Masuk?
                </h3>

                <p class="text-sm text-[#475569] font-semibold mt-2">
                    Data barang masuk yang dipilih akan dihapus.
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            <div class="flex items-center justify-center gap-3">

                <button type="button" onclick="closeDeleteModal()"
                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200
                           text-[#475569] font-black rounded-xl transition">
                    Batal
                </button>

                <button type="button" onclick="submitDeleteForm()"
                    class="px-6 py-3 bg-rose-600 hover:bg-rose-700
                           text-white font-black rounded-xl transition">
                    <i class="fas fa-trash-can mr-1"></i>
                    Hapus
                </button>

            </div>

        </div>

    </div>


    {{-- JAVASCRIPT --}}
    <script>
        let deleteId = null;

        function openDeleteModal(id) {
            deleteId = id;

            const modal = document.getElementById('delete-modal');
            const content = document.getElementById('delete-modal-content');

            modal.classList.remove('hidden');

            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 30);
        }

        function closeDeleteModal() {
            const modal = document.getElementById('delete-modal');
            const content = document.getElementById('delete-modal-content');

            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                deleteId = null;
            }, 180);
        }

        function submitDeleteForm() {
            if (!deleteId) {
                return;
            }

            const form = document.getElementById('delete-form-' + deleteId);

            if (form) {
                form.submit();
            }
        }

        document.getElementById('delete-modal').addEventListener('click', function(event) {
            if (event.target === this) {
                closeDeleteModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (
                event.key === 'Escape' &&
                !document.getElementById('delete-modal').classList.contains('hidden')
            ) {
                closeDeleteModal();
            }
        });
    </script>
@endsection


<style>
    input:focus,
    select:focus {
        outline: none;
        border-color: #0B4F35 !important;
        box-shadow: 0 0 0 3px rgba(11, 79, 53, .12);
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem !important;
            padding-right: 1rem !important;
        }
    }
</style>

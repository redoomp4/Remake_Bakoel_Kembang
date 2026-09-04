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
        "
    >
        <div
            style="
                width:100%;
                max-width:1250px;
                margin:0 auto;
                box-sizing:border-box;
            "
        >
            {{-- HEADER --}}
            <div
                style="
                    display:flex;
                    justify-content:space-between;
                    align-items:center;
                    gap:1rem;
                    flex-wrap:wrap;
                    margin-bottom:1.5rem;
                "
            >
                <div style="min-width:0;">
                    <p
                        style="
                            color:#8FA882;
                            font-weight:800;
                            font-size:.75rem;
                            text-transform:uppercase;
                            margin:0 0 .25rem 0;
                        "
                    >
                        Inventori · Master Barang
                    </p>
                    <h4
                        style="
                            color:#0B4F35;
                            font-size:1.9rem;
                            font-weight:900;
                            margin:0;
                        "
                    >
                        Daftar Item
                    </h4>
                </div>
                <a href="{{ route('item.create') }}"
                    style="
                        background:#0B4F35;
                        color:#fff;
                        padding:.8rem 1.25rem;
                        border-radius:.85rem;
                        text-decoration:none;
                        font-weight:800;
                        white-space:nowrap;
                    "
                >
                    <i class="fas fa-plus"></i>
                    Tambah Item Baru
                </a>
            </div>

            {{-- FILTER --}}
            <form method="GET" action="{{ route('item.index') }}"
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
                "
            >
                {{-- SEARCH --}}
                <div style="flex:1 1 280px;min-width:0;">
                    <label for="search"
                        style="
                            display:block;
                            color:#475569;
                            font-weight:800;
                            font-size:.85rem;
                            margin-bottom:.5rem;
                        "
                    >
                        Cari Barang
                    </label>
                    <input type="text" id="search" name="search"
                        placeholder="Cari nama atau kode barang..."
                        value="{{ request('search') }}"
                        style="
                            width:100%;
                            box-sizing:border-box;
                            padding:.8rem;
                            border:1px solid #E4E4D9;
                            border-radius:.75rem;
                        "
                    >
                </div>

                {{-- KATEGORI --}}
                <div style="flex:1 1 280px;min-width:0;">
                    <label for="kategori"
                        style="
                            display:block;
                            color:#475569;
                            font-weight:800;
                            font-size:.85rem;
                            margin-bottom:.5rem;
                        "
                    >
                        Kategori
                    </label>
                    <select name="kategori" id="kategori"
                        style="
                            width:100%;
                            box-sizing:border-box;
                            padding:.8rem;
                            border:1px solid #E4E4D9;
                            border-radius:.75rem;
                            background:#fff;
                        "
                    >
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($kategoris as $kategori)
                            <option value="{{ $kategori->id }}"
                                {{ request('kategori') == $kategori->id ? 'selected' : '' }}
                            >
                                {{ $kategori->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- BUTTON --}}
                <div style="display:flex;gap:.75rem;flex-shrink:0;">
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
                        "
                    >
                        <i class="fas fa-filter"></i>
                        Filter
                    </button>
                    <a href="{{ route('item.index') }}"
                        style="
                            background:#E4E4D9;
                            color:#475569;
                            border-radius:.75rem;
                            padding:.8rem 1.25rem;
                            text-decoration:none;
                            font-weight:800;
                            white-space:nowrap;
                        "
                    >
                        Reset
                    </a>
                </div>
            </form>

            {{-- TABLE --}}
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
                "
            >
                <table
                    style="
                        width:100%;
                        min-width:1050px;
                        border-collapse:collapse;
                        margin:0;
                    "
                >
                    <thead
                        style="
                            background:#f0ebe3;
                            color:#475569;
                            font-size:.68rem;
                            text-transform:uppercase;
                            letter-spacing:.05em;
                        "
                    >
                        <tr>
                            <th style="padding:.75rem;text-align:left;white-space:nowrap;">No</th>
                            <th style="padding:.75rem;text-align:left;white-space:nowrap;">Foto</th>
                            <th style="padding:.75rem;text-align:left;white-space:nowrap;">Kode Barang</th>
                            <th style="padding:.75rem;text-align:left;">Nama Barang</th>
                            <th style="padding:.75rem;text-align:left;white-space:nowrap;">Kategori</th>
                            <th style="padding:.75rem;text-align:left;white-space:nowrap;">Satuan</th>
                            <th style="padding:.75rem;text-align:center;white-space:nowrap;">Stok Minimum</th>
                            <th style="padding:.75rem;text-align:center;white-space:nowrap;">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($items as $item)
                            <tr
                                style="border-bottom:1px solid #E4E4D9;font-size:.82rem;"
                                onmouseover="this.style.background='#fafbf8'"
                                onmouseout="this.style.background='#fff'"
                            >
                                {{-- NO --}}
                                <td style="padding:.75rem;text-align:center;color:#64748b;font-weight:700;">
                                    {{ $items->firstItem() + $loop->index }}
                                </td>

                                {{-- FOTO --}}
                                <td style="padding:.75rem;">
                                    @if ($item->foto)
                                        <img src="{{ asset('storage/' . $item->foto) }}"
                                            alt="Foto {{ $item->nama_barang }}"
                                            style="
                                                width:44px;
                                                height:44px;
                                                border-radius:.75rem;
                                                object-fit:cover;
                                                border:1px solid #E4E4D9;
                                            "
                                        >
                                    @else
                                        <span style="color:#999;font-size:.75rem;">
                                            -
                                        </span>
                                    @endif
                                </td>

                                {{-- KODE --}}
                                <td style="padding:.75rem;font-weight:800;white-space:nowrap;color:#0B4F35;">
                                    {{ $item->kode_barang }}
                                </td>

                                {{-- NAMA --}}
                                <td style="padding:.75rem;max-width:180px;word-break:break-word;">
                                    {{ $item->nama_barang }}
                                </td>

                                {{-- KATEGORI --}}
                                <td style="padding:.75rem;">
                                    <span
                                        style="
                                            display:inline-block;
                                            background:#f1f5f9;
                                            color:#475569;
                                            padding:.35rem .6rem;
                                            border-radius:999px;
                                            font-size:.68rem;
                                            font-weight:800;
                                            white-space:nowrap;
                                        "
                                    >
                                        {{ $item->kategori->kategori ?? '-' }}
                                    </span>
                                </td>

                                {{-- SATUAN --}}
                                <td style="padding:.75rem;white-space:nowrap;">
                                    {{ $item->satuan->nama_satuan ?? '-' }}
                                </td>

                                {{-- STOK MINIMUM --}}
                                <td style="padding:.75rem;text-align:center;white-space:nowrap;">
                                    {{ number_format($item->stok_minimum, 0, ',', '.') }}
                                </td>

                                {{-- AKSI --}}
                                <td style="padding:.75rem;white-space:nowrap;">
                                    <div
                                        style="
                                            display:flex;
                                            gap:.35rem;
                                            align-items:center;
                                            justify-content:center;
                                        "
                                    >
                                        {{-- DETAIL --}}
                                        <a href="{{ route('item.show', $item->id) }}" title="Lihat Detail"
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
                                            "
                                        >
                                            <i class="fas fa-eye" style="font-size:.8rem;"></i>
                                        </a>

                                        {{-- EDIT --}}
                                        <a href="{{ route('item.edit', $item->id) }}" title="Edit"
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
                                            "
                                        >
                                            <i class="fas fa-pen" style="font-size:.8rem;"></i>
                                        </a>

                                        {{-- Cetak QR --}}
                                        @if ($item->qr_code)
                                            <a href="{{ route('item.cetak.pdf', $item->id) }}" target="_blank"
                                                title="Cetak QR Code"
                                                style="
                                                    background:#10b981;
                                                    color:#fff;
                                                    width:32px;
                                                    height:32px;
                                                    min-width:32px;
                                                    border-radius:.55rem;
                                                    text-decoration:none;
                                                    display:inline-flex;
                                                    align-items:center;
                                                    justify-content:center;
                                                "
                                            >
                                                <i class="fas fa-qrcode" style="font-size:.8rem;"></i>
                                            </a>
                                        @endif

                                        {{-- HAPUS --}}
                                        <form action="{{ route('item.destroy', $item->id) }}" method="POST"
                                            class="delete-form"
                                            data-item="{{ $item->nama_barang }}"
                                            style="
                                                display:inline-flex;
                                                width:32px;
                                                min-width:32px;
                                                height:32px;
                                                margin:0;
                                                padding:0;
                                            "
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" title="Hapus" onclick="openDeleteModal(this)"
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
                                                "
                                            >
                                                <i class="fas fa-trash" style="font-size:.8rem;"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8"
                                    style="
                                        padding:3rem 1rem;
                                        text-align:center;
                                        color:#999;
                                    "
                                >
                                    <i class="fas fa-box-open"
                                        style="
                                            font-size:1.8rem;
                                            margin-bottom:.75rem;
                                        "
                                    >
                                    </i>
                                    <div style="font-weight:700;">
                                        Data item belum tersedia
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div style="width:100%;margin-top:1.5rem;text-align:center;">
                {{ $items->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- DELETE CONFIRMATION MODAL --}}
    <div id="delete-modal"
        class="fixed inset-0 bg-brand-emerald/40 backdrop-blur-sm
               flex items-center justify-center z-[999] p-4 hidden"
        role="dialog" aria-modal="true"
    >
        <div id="delete-modal-content"
            class="bg-white text-gray-900
                   border-2 border-brand-emerald
                   p-7 md:p-10 rounded-3xl shadow-2xl
                   max-w-xl w-full text-center space-y-5
                   transform scale-95 transition-transform duration-300"
        >
            {{-- ICON --}}
            <div
                class="w-16 h-16 bg-rose-50 rounded-2xl
                       flex items-center justify-center
                       text-rose-600 mx-auto"
            >
                <i class="fas fa-trash-can text-3xl"></i>
            </div>

            {{-- TITLE --}}
            <div>
                <h3 id="delete-title" class="text-xl md:text-2xl font-black text-[#0B4F35]">
                    Hapus Item?
                </h3>
                <p class="text-sm text-[#475569] font-semibold mt-2">
                    Data item yang dipilih akan dihapus.
                    Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>

            {{-- ITEM --}}
            <div class="bg-[#FAF9F6]
                    border border-[#E4E4D9]
                    rounded-2xl p-4">
                <p class="text-sm text-[#475569] font-semibold">
                    Apakah Anda yakin ingin menghapus item:
                </p>
                <p id="deleteItemName" class="text-base font-black text-[#0B4F35] mt-1">
                </p>
            </div>

            {{-- BUTTON --}}
            <div class="flex items-center justify-center gap-3 pt-1">
                <button type="button" onclick="closeDeleteModal()"
                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200
                           text-[#475569] font-black rounded-xl transition"
                >
                    Batal
                </button>
                <button type="button" onclick="submitDelete()"
                    class="px-6 py-3 bg-rose-600 hover:bg-rose-700
                           text-white font-black rounded-xl transition"
                >
                    <i class="fas fa-trash-can mr-1"></i>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- JAVASCRIPT --}}
    <script>
        let deleteForm = null;

        function openDeleteModal(button) {
            deleteForm = button.closest('.delete-form');
            const itemName = deleteForm.dataset.item;
            document.getElementById('deleteItemName').textContent = itemName;

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
                deleteForm = null;
            }, 180);
        }

        function submitDelete() {
            if (deleteForm) {
                deleteForm.submit();
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

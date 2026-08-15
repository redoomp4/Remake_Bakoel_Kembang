@extends('layouts.app')
@section('content')
    <div style="background:#FAF9F6;min-height:100vh;padding:2rem 1.25rem;">
        <div style="max-width:1250px;margin:auto;">


            <div
                style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div>
                    <p style="color:#8FA882;font-weight:800;font-size:.75rem;text-transform:uppercase;">Inventori · Arus
                        Masuk</p>
                    <h4 style="color:#0B4F35;font-size:1.9rem;font-weight:900;margin:0;">Daftar Barang Masuk</h4>
                </div><a href="{{ route('barang-masuk.create') }}"
                    style="background:#0B4F35;color:#fff;padding:.8rem 1.25rem;border-radius:.85rem;text-decoration:none;font-weight:800;">+
                    Tambah Barang Masuk</a>
            </div>
            <form method="GET" action="{{ route('barang-masuk.index') }}"
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.25rem;padding:1.25rem;display:flex;gap:1rem;align-items:end;flex-wrap:wrap;margin-bottom:1.5rem;">
                <div style="flex:1;min-width:220px;"><label for="search"
                        style="display:block;color:#475569;font-weight:800;font-size:.85rem;margin-bottom:.5rem;">Cari
                        Barang</label><input type="text" id="search" name="search" placeholder="Cari nama barang"
                        value="{{ request('search') }}"
                        style="width:100%;padding:.8rem;border:1px solid #E4E4D9;border-radius:.75rem;"></div>
                <div style="flex:1;min-width:220px;"><label for="lokasi"
                        style="display:block;color:#475569;font-weight:800;font-size:.85rem;margin-bottom:.5rem;">Lokasi</label><select
                        name="lokasi" id="lokasi"
                        style="width:100%;padding:.8rem;border:1px solid #E4E4D9;border-radius:.75rem;">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach ($lokasis as $lokasi)
                            <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                                {{ $lokasi->nama_lokasi }}</option>
                        @endforeach
                    </select>
                </div><button type="submit"
                    style="background:#0B4F35;color:#fff;border:0;border-radius:.75rem;padding:.8rem 1.25rem;font-weight:800;">Filter</button><a
                    href="{{ route('barang-masuk.index') }}"
                    style="background:#E4E4D9;color:#475569;border-radius:.75rem;padding:.8rem 1.25rem;text-decoration:none;font-weight:800;">Reset</a>
            </form>
            <div
                style="background:#fff;border:1px solid #E4E4D9;border-radius:1.5rem;overflow:hidden;box-shadow:0 1px 4px rgba(0,0,0,.05);overflow-x:auto;">
                <table style="width:100%;border-collapse:collapse;table-layout:auto;">
                    <thead
                        style="background:#f0ebe3;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;">
                        <tr>
                            @foreach ([
            'No',
            'Tanggal Masuk',
            'Kode Barang',
            'Nama Barang',
            'Jumlah',
            // 'Harga Beli',
            'Total Harga',
            // 'Kadaluarsa',
            'Pemasok',
            'Lokasi',
            'Kondisi',
            'Detail',
        ] as $heading)
                                <th style="padding:1rem;text-align:left;">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangMasuks as $bm)
                            <tr style="border-bottom:1px solid #E4E4D9;" onmouseover="this.style.background='#fafbf8'"
                                onmouseout="this.style.background='#fff'">
                                <td style="padding:1rem;">{{ $loop->iteration }}</td>
                                <td style="padding:1rem;">
                                    {{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d-m-Y H:i') }}</td>
                                <td style="padding:1rem;font-weight:800;">{{ $bm->item->kode_barang }}</td>
                                <td style="padding:1rem;">{{ $bm->item->nama_barang ?? '-' }}</td>
                                <td style="padding:1rem;">{{ $bm->jumlah }}</td>
                                {{-- <td style="padding:1rem;">Rp {{ number_format($bm->harga_satuan, 0, ',', '.') }}</td> --}}
                                <td style="padding:1rem;">Rp {{ number_format($bm->total_harga, 0, ',', '.') }}</td>
                                {{-- <td style="padding:1rem;">{{ \Carbon\Carbon::parse($bm->tanggal_kadaluarsa)->format('d-m-Y H:i') }}</td> --}}
                                <td style="padding:1rem;">{{ $bm->pemasok->nama_pemasok ?? '-' }}</td>
                                <td style="padding:1rem;">{{ $bm->lokasi->nama_lokasi ?? '-' }}</td>
                                <td style="padding:1rem;"><span
                                        style="background:#ecfdf5;color:#047857;padding:.4rem .7rem;border-radius:999px;font-size:.75rem;font-weight:800;">{{ $bm->kondisi->nama_kondisi ?? '-' }}</span>
                                </td>

                                <td style="padding:1rem;white-space:nowrap;">
                                    <div
                                        style="display:flex;gap:.4rem;flex-wrap:nowrap;align-items:center;justify-content:flex-start;">

                                        {{-- Detail --}}
                                        <a href="{{ route('barang-masuk.detail', $bm->id) }}" title="Lihat Detail"
                                            style="background:#60a5fa;color:#fff;
                                            width:36px;height:36px;
                                            min-width:36px;
                                            border-radius:.6rem;
                                            text-decoration:none;
                                            font-weight:700;
                                            display:inline-flex;
                                            align-items:center;
                                            justify-content:center;">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('barang-masuk.edit', $bm->id) }}" title="Edit"
                                            style="background:#fbbf24;color:#1a1a1a;
                                                        width:36px;height:36px;
                                                        min-width:36px;
                                                        border-radius:.6rem;
                                                        text-decoration:none;
                                                        font-weight:700;
                                                        display:inline-flex;
                                                        align-items:center;
                                                        justify-content:center;">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        {{-- Hapus --}} <form id="delete-form-{{ $bm->id }}"
                                            action="{{ route('barang-masuk.destroy', $bm->id) }}" method="POST"
                                            style=" display:inline-flex; width:36px; min-width:36px; height:36px; margin:0; padding:0; vertical-align:middle; ">
                                            @csrf @method('DELETE') <button type="button" title="Hapus"
                                                onclick="openDeleteModal({{ $bm->id }})"
                                                style=" background:#dc2626; color:#fff; width:36px; min-width:36px; height:36px; padding:0; margin:0; border:0; border-radius:.6rem; font-weight:700; cursor:pointer; display:inline-flex; align-items:center; justify-content:center; vertical-align:middle; appearance:none; -webkit-appearance:none; ">
                                                <i class="fas fa-trash"></i> </button> </form>

                                    </div>
                                </td>


                        </tr>@empty<tr>
                                <td colspan="12" style="padding:2rem;text-align:center;color:#999;">Data belum tersedia.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1.5rem;text-align:center;">{{ $barangMasuks->links() }}</div>

        </div>
    </div>

    {{-- MODAL CONFIRM DELETE --}} {{-- ========================================================= --}} <div id="delete-modal"
        class="fixed inset-0 bg-brand-emerald/40 backdrop-blur-sm flex items-center justify-center z-[999] p-4 hidden"
        role="dialog" aria-modal="true">
        <div id="delete-modal-content"
            class="bg-white text-gray-900 border-2 border-brand-emerald p-7 md:p-10 rounded-3xl shadow-2xl max-w-xl w-full text-center space-y-5 transform scale-95 transition-transform duration-300">
            {{-- ICON --}} <div
                class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600 mx-auto"> <i
                    class="fas fa-trash-can text-3xl"></i> </div> {{-- TITLE + MESSAGE --}} <div>
                <h3 class="text-xl md:text-2xl font-black text-[#0B4F35]"> Hapus Barang Masuk? </h3>
                <p class="text-sm text-[#475569] font-semibold mt-2"> Data barang masuk yang dipilih akan dihapus. Tindakan
                    ini tidak dapat dibatalkan. </p>
            </div> {{-- BUTTON --}} <div class="flex items-center justify-center gap-3"> <button type="button"
                    onclick="closeDeleteModal()"
                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-[#475569] font-black rounded-xl transition"> Batal
                </button> <button type="button" onclick="submitDeleteForm()"
                    class="px-6 py-3 bg-rose-600 hover:bg-rose-700 text-white font-black rounded-xl transition"> <i
                        class="fas fa-trash-can mr-1"></i> Hapus </button> </div>
        </div>
    </div>

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
        } // Klik area luar modal = tutup document.getElementById('delete-modal') .addEventListener('click', function(event) { if (event.target === this) { closeDeleteModal(); } }); // ESC = tutup document.addEventListener('keydown', function(event) { if ( event.key === 'Escape' && !document .getElementById('delete-modal') .classList.contains('hidden') ) { closeDeleteModal(); } }); 
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

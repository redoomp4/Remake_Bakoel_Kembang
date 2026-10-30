<section id="tab-content-master" class="oneshot-tab-content" role="tabpanel">
    <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @php
            $masters = [
                [
                    'title' => 'Satuan',
                    'route' => 'satuan.store',
                    'fields' => [['name' => 'nama_satuan', 'label' => 'Nama Satuan', 'type' => 'text']],
                ],
                [
                    'title' => 'Kategori',
                    'route' => 'kategori.store',
                    'fields' => [
                        ['name' => 'kategori', 'label' => 'Nama Kategori', 'type' => 'text'],
                        ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea'],
                    ],
                ],
                [
                    'title' => 'Pemasok',
                    'route' => 'pemasok.store',
                    'fields' => [
                        ['name' => 'nama_pemasok', 'label' => 'Nama Pemasok', 'type' => 'text'],
                        ['name' => 'email', 'label' => 'Email', 'type' => 'email'],
                        ['name' => 'no_telepon', 'label' => 'No. Telepon', 'type' => 'text'],
                        ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea'],
                        ['name' => 'jenis', 'label' => 'Jenis', 'type' => 'text'],
                        ['name' => 'bergabung_sejak', 'label' => 'Bergabung Sejak', 'type' => 'date'],
                        ['name' => 'nama_pic', 'label' => 'Nama PIC', 'type' => 'text'],
                    ],
                ],
                [
                    'title' => 'Lokasi',
                    'route' => 'lokasi.store',
                    'fields' => [
                        ['name' => 'nama_lokasi', 'label' => 'Nama Lokasi', 'type' => 'text'],
                        ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea'],
                    ],
                ],
                [
                    'title' => 'Kondisi',
                    'route' => 'kondisi.store',
                    'fields' => [
                        ['name' => 'nama_kondisi', 'label' => 'Nama Kondisi', 'type' => 'text'],
                        ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea'],
                    ],
                ],
            ];
        @endphp

        @foreach ($masters as $master)
            <form action="{{ route($master['route']) }}" method="POST" class="ajax-micro-form rounded-3xl border border-brand-accent bg-white p-5 shadow-sm">
                @csrf
                <div class="mb-5 flex items-center justify-between gap-3">
                    <h2 class="text-lg font-black text-brand-emerald">{{ $master['title'] }}</h2>
                    <span class="rounded-lg bg-brand-offwhite px-2 py-1 text-[10px] font-black uppercase tracking-widest text-brand-slate">Quick Input</span>
                </div>
                <div class="space-y-4">
                    @foreach ($master['fields'] as $field)
                        <div>
                            <label for="{{ $master['title'] }}-{{ $field['name'] }}" class="field-label">{{ $field['label'] }}</label>
                            @if ($field['type'] === 'textarea')
                                <textarea id="{{ $master['title'] }}-{{ $field['name'] }}" name="{{ $field['name'] }}" rows="2" class="field-input"></textarea>
                            @else
                                <input id="{{ $master['title'] }}-{{ $field['name'] }}" type="{{ $field['type'] }}" name="{{ $field['name'] }}" class="field-input">
                            @endif
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-brand-emerald px-4 py-3 text-sm font-black text-white transition hover:bg-brand-slate">
                    <i class="fas fa-plus" aria-hidden="true"></i>Simpan {{ $master['title'] }}
                </button>
            </form>
        @endforeach
    </div>

    {{-- Script khusus tab master --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // AJAX submit untuk micro form master
            document.querySelectorAll('.ajax-micro-form').forEach((form) => {
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': window.csrfToken,
                                'Accept': 'application/json'
                            },
                            body: new FormData(form)
                        });
                        const data = await response.json().catch(() => ({}));
                        if (!response.ok) throw new Error(data.message || 'Gagal simpan');

                        form.reset();
                        window.toast('Data master berhasil ditambahkan.');

                        // Refresh dropdown di semua tab
                        await refreshDropdowns();
                    } catch (error) {
                        window.toast(error.message || 'Terjadi kesalahan.', 'error');
                    }
                });
            });

            // Fungsi refresh dropdown (dipakai juga di tab item)
            window.refreshDropdowns = async function() {
                try {
                    const res = await fetch('{{ route('form.options') }}', {
                        headers: { Accept: 'application/json' }
                    });
                    const data = await res.json();
                    if (!data.success) return;

                    // Update standard dropdowns
                    [
                        ['.select-pemasok', data.pemasoks],
                        ['.select-lokasi', data.lokasis],
                        ['.select-kondisi', data.kondisis]
                    ].forEach(([sel, items]) => {
                        document.querySelectorAll(sel).forEach((select) => {
                            const val = select.value;
                            select.innerHTML = '<option value="">-- Pilih --</option>';
                            (items || []).forEach((item) => {
                                const opt = new Option(item.nama, item.id);
                                if (String(item.id) === String(val)) opt.selected = true;
                                select.add(opt);
                            });
                        });
                    });

                    // Update item dropdowns
                    document.querySelectorAll('.select-item-kode').forEach((select) => {
                        const val = select.value;
                        select.innerHTML = '<option value="">-- Pilih Barang --</option>';
                        (data.items || []).forEach((item) => {
                            const opt = new Option(`${item.kode_barang} — ${item.nama_barang}`, item.kode_barang);
                            if (item.kode_barang === val) opt.selected = true;
                            select.add(opt);
                        });
                    });

                    // Update hybrid options
                    const updateHybrid = (id, items) => {
                        const wrapper = document.getElementById(id);
                        if (!wrapper) return;
                        const list = wrapper.querySelector('.hybrid-options');
                        if (!list) return;
                        list.innerHTML = '';
                        (items || []).forEach((item) => {
                            const li = document.createElement('li');
                            li.className = 'hybrid-option cursor-pointer px-4 py-2 text-sm hover:bg-emerald-50 hover:text-emerald-700 font-medium';
                            li.dataset.value = item.nama;
                            li.textContent = item.nama;
                            list.appendChild(li);
                        });
                    };

                    updateHybrid('hybrid-kategori', data.kategories);
                    updateHybrid('hybrid-satuan', data.satuans);

                } catch (e) {
                    // ignore
                }
            };
        });
    </script>
</section>

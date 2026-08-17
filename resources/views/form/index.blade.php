@extends('layouts.app')
@if(session('success'))
  <div class="mb-6 rounded-2xl border border-emerald-300 bg-emerald-100 p-4 text-sm font-bold text-emerald-800">
    {{ session('success') }}
  </div>
@endif

@if($errors->any())
  <div class="mb-6 rounded-2xl border border-rose-300 bg-rose-100 p-4 text-sm font-bold text-rose-800">
    <p class="font-black mb-1">Gagal Menyimpan Data:</p>
    <ul class="list-disc pl-5 space-y-1">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif
@section('content')
<div class="min-h-full bg-brand-offwhite">
  <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
    <header class="mb-8 flex flex-col gap-4 border-b border-brand-accent/60 pb-6 sm:flex-row sm:items-end sm:justify-between">
      <div>
        <p class="mb-2 text-xs font-black uppercase tracking-[0.2em] text-brand-slate">Operasional Gudang</p>
        <h1 class="text-3xl font-black tracking-tight text-brand-emerald">Form Oneshot Master & Transaksi</h1>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-brand-slate">Input master data, item baru, dan transaksi tanpa berpindah halaman.</p>
      </div>
      <a href="{{ url()->previous() }}" class="inline-flex w-fit items-center gap-2 rounded-xl border border-brand-accent bg-white px-4 py-3 text-sm font-bold text-brand-slate shadow-sm transition hover:border-brand-emerald hover:text-brand-emerald">
        <i class="fas fa-arrow-left" aria-hidden="true"></i><span>Kembali</span>
      </a>
    </header>

    <!-- Tab Navigation Bar -->
    <div class="mb-6 overflow-x-auto rounded-2xl border border-brand-accent bg-white p-2 shadow-sm" role="tablist">
      <div class="flex min-w-max gap-2">
        <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-emerald-600 bg-emerald-50 px-4 py-3 text-sm font-black text-emerald-700" data-tab="master" role="tab" aria-selected="true">
          <i class="fas fa-bolt mr-2" aria-hidden="true"></i>Master Quick Add
        </button>
        <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-transparent px-4 py-3 text-sm font-bold text-brand-slate hover:bg-brand-offwhite" data-tab="item" role="tab" aria-selected="false">
          <i class="fas fa-box mr-2" aria-hidden="true"></i>Tambah Item Baru
        </button>
        <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-transparent px-4 py-3 text-sm font-bold text-brand-slate hover:bg-brand-offwhite" data-tab="masuk" role="tab" aria-selected="false">
          <i class="fas fa-arrow-down mr-2" aria-hidden="true"></i>Transaksi Masuk
        </button>
        <button type="button" class="oneshot-tab-btn rounded-xl border-b-2 border-transparent px-4 py-3 text-sm font-bold text-brand-slate hover:bg-brand-offwhite" data-tab="keluar" role="tab" aria-selected="false">
          <i class="fas fa-arrow-up mr-2" aria-hidden="true"></i>Transaksi Keluar
        </button>
      </div>
    </div>

    <!-- TAB 1: MASTER QUICK ADD -->
    <section id="tab-content-master" class="oneshot-tab-content" role="tabpanel">
      <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
        @php
          $masters = [
            ['title' => 'Satuan', 'route' => 'satuan.store', 'fields' => [['name' => 'nama_satuan', 'label' => 'Nama Satuan', 'type' => 'text']]],
            ['title' => 'Kategori', 'route' => 'kategori.store', 'fields' => [['name' => 'kategori', 'label' => 'Nama Kategori', 'type' => 'text'], ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea']]],
            ['title' => 'Pemasok', 'route' => 'pemasok.store', 'fields' => [['name' => 'nama_pemasok', 'label' => 'Nama Pemasok', 'type' => 'text'], ['name' => 'email', 'label' => 'Email', 'type' => 'email'], ['name' => 'no_telepon', 'label' => 'No. Telepon', 'type' => 'text'], ['name' => 'alamat', 'label' => 'Alamat', 'type' => 'textarea'], ['name' => 'jenis', 'label' => 'Jenis', 'type' => 'text'], ['name' => 'bergabung_sejak', 'label' => 'Bergabung Sejak', 'type' => 'date'], ['name' => 'nama_pic', 'label' => 'Nama PIC', 'type' => 'text']]],
            ['title' => 'Lokasi', 'route' => 'lokasi.store', 'fields' => [['name' => 'nama_lokasi', 'label' => 'Nama Lokasi', 'type' => 'text'], ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea']]],
            ['title' => 'Kondisi', 'route' => 'kondisi.store', 'fields' => [['name' => 'nama_kondisi', 'label' => 'Nama Kondisi', 'type' => 'text'], ['name' => 'deskripsi', 'label' => 'Deskripsi', 'type' => 'textarea']]],
          ];
        @endphp
        @foreach($masters as $master)
          <form action="{{ route($master['route']) }}" method="POST" class="ajax-micro-form rounded-3xl border border-brand-accent bg-white p-5 shadow-sm">
            @csrf
            <div class="mb-5 flex items-center justify-between gap-3">
              <h2 class="text-lg font-black text-brand-emerald">{{ $master['title'] }}</h2>
              <span class="rounded-lg bg-brand-offwhite px-2 py-1 text-[10px] font-black uppercase tracking-widest text-brand-slate">Quick Input</span>
            </div>
            <div class="space-y-4">
              @foreach($master['fields'] as $field)
                <div>
                  <label for="{{ $master['title'] }}-{{ $field['name'] }}" class="field-label">{{ $field['label'] }}</label>
                  @if($field['type'] === 'textarea')
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
    </section>

    <!-- TAB 2: TAMBAH ITEM BARU (HYBRID DROPDOWN) -->
    <section id="tab-content-item" class="oneshot-tab-content hidden" role="tabpanel">
      <form action="{{ route('form.item.store') }}" method="POST" enctype="multipart/form-data" class="rounded-3xl border border-brand-accent bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <h2 class="mb-6 text-xl font-black text-brand-emerald">Tambah Item Baru</h2>
        <div class="grid gap-5 md:grid-cols-2">
          <div>
            <label class="field-label">Nama Barang</label>
            <input name="nama_barang" required class="field-input" placeholder="Masukkan nama barang">
          </div>

          <!-- Kategori: Hybrid Dropdown -->
          <div>
  <label class="field-label">Kategori (Pilih atau Ketik Baru)</label>
  <div class="relative hybrid-select" id="hybrid-kategori">
    <input 
      type="text" 
      name="kategori_input" 
      id="kategori_input"
      value="{{ old('kategori_input') }}"
      required 
      class="field-input pr-10 hybrid-input" 
      placeholder="-- Pilih / Ketik Kategori --"
      autocomplete="off"
    >
    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-brand-slate hybrid-toggle">
      <i class="fas fa-chevron-down text-xs pointer-events-none"></i>
    </button>
    <ul class="hybrid-options hidden absolute z-50 left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-xl border border-brand-accent bg-white py-1 shadow-lg text-brand-slate">
      @foreach($kategories ?? [] as $item)
        <li class="hybrid-option cursor-pointer px-4 py-2 text-sm hover:bg-emerald-50 hover:text-emerald-700 font-medium" data-value="{{ $item->kategori }}">
          {{ $item->kategori }}
        </li>
      @endforeach
    </ul>
  </div>
</div>

          <!-- Satuan: Hybrid Dropdown -->
          <div>
  <label class="field-label">Satuan (Pilih atau Ketik Baru)</label>
  <div class="relative hybrid-select" id="hybrid-satuan">
    <input 
      type="text" 
      name="satuan_input" 
      id="satuan_input"
      value="{{ old('satuan_input') }}"
      required 
      class="field-input pr-10 hybrid-input" 
      placeholder="-- Pilih / Ketik Satuan --"
      autocomplete="off"
    >
    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-3 text-brand-slate hybrid-toggle">
      <i class="fas fa-chevron-down text-xs pointer-events-none"></i>
    </button>
    <ul class="hybrid-options hidden absolute z-50 left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-xl border border-brand-accent bg-white py-1 shadow-lg text-brand-slate">
      @foreach($satuans ?? [] as $item)
        <li class="hybrid-option cursor-pointer px-4 py-2 text-sm hover:bg-emerald-50 hover:text-emerald-700 font-medium" data-value="{{ $item->nama_satuan }}">
          {{ $item->nama_satuan }}
        </li>
      @endforeach
    </ul>
  </div>
</div>

          <div>
            <label class="field-label">Stok Minimum</label>
            <input name="stok_minimum" type="number" min="0" required class="field-input" value="0">
          </div>
          <div>
            <label class="field-label">Harga Dasar (Rp)</label>
            <input name="harga_dasar" type="number" min="0" step="0.01" required class="field-input" placeholder="0.00">
          </div>
          <div>
            <label class="field-label">Foto Item</label>
            <input name="foto" type="file" accept="image/*" class="field-input">
          </div>
          <div class="md:col-span-2">
            <label class="field-label">Deskripsi Item</label>
            <textarea name="deskripsi" rows="3" class="field-input" placeholder="Catatan/deskripsi tambahan item"></textarea>
          </div>
        </div>
        <button type="submit" class="mt-6 rounded-xl bg-brand-emerald px-5 py-3 text-sm font-black text-white transition hover:bg-brand-slate">
          <i class="fas fa-save mr-2"></i>Simpan Item
        </button>
      </form>
    </section>

    <!-- TAB 3: TRANSAKSI BARANG MASUK -->
    <section id="tab-content-masuk" class="oneshot-tab-content hidden" role="tabpanel">
      <form action="{{ route('barang-masuk.store') }}" method="POST" class="rounded-3xl border border-brand-accent bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <h2 class="mb-6 text-xl font-black text-brand-emerald">Transaksi Barang Masuk</h2>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          <div>
            <label class="field-label">Pilih Barang</label>
            <select name="kode_barang" required class="field-input select-item-kode">
              <option value="">-- Pilih Barang --</option>
              @foreach($items ?? [] as $item)
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
              @foreach($pemasoks ?? [] as $item)
                <option value="{{ $item->id }}">{{ $item->nama_pemasok }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="field-label">Lokasi Penyimpanan</label>
            <select name="id_lokasi" required class="field-input select-lokasi">
              <option value="">-- Pilih Lokasi --</option>
              @foreach($lokasis ?? [] as $item)
                <option value="{{ $item->id }}">{{ $item->nama_lokasi }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="field-label">Kondisi Barang</label>
            <select name="id_kondisi" required class="field-input select-kondisi">
              <option value="">-- Pilih Kondisi --</option>
              @foreach($kondisis ?? [] as $item)
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

    <!-- TAB 4: TRANSAKSI BARANG KELUAR -->
    <section id="tab-content-keluar" class="oneshot-tab-content hidden" role="tabpanel">
      <form action="{{ route('barang-keluar.store') }}" method="POST" class="rounded-3xl border border-brand-accent bg-white p-5 shadow-sm sm:p-7">
        @csrf
        <h2 class="mb-4 text-xl font-black text-brand-emerald">Transaksi Barang Keluar</h2>
        <div class="mb-5 flex min-h-8 items-center">
          <span id="live-stock-badge" class="text-xs font-black text-brand-slate" aria-live="polite"></span>
        </div>
        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          <div>
            <label class="field-label">Pilih Barang</label>
            <select id="select-item-outbound" name="kode_barang" required class="field-input select-item-kode">
              <option value="">-- Pilih Barang --</option>
              @foreach($items ?? [] as $item)
                <option value="{{ $item->kode_barang }}">{{ $item->kode_barang }} — {{ $item->nama_barang }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="field-label">Lokasi Asal</label>
            <select name="id_lokasi" required class="field-input select-lokasi">
              <option value="">-- Pilih Lokasi --</option>
              @foreach($lokasis ?? [] as $item)
                <option value="{{ $item->id }}">{{ $item->nama_lokasi }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="field-label">Kondisi Barang</label>
            <select name="id_kondisi" required class="field-input select-kondisi">
              <option value="">-- Pilih Kondisi --</option>
              @foreach($kondisis ?? [] as $item)
                <option value="{{ $item->id }}">{{ $item->nama_kondisi }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="field-label">Jumlah Keluar</label>
            <input name="jumlah_keluar" type="number" min="1" required class="field-input">
          </div>
          <div>
            <label class="field-label">Harga Jual</label>
            <input id="input-harga-jual" name="harga_jual" type="number" min="0" step="0.01" required class="field-input">
          </div>
          <div>
            <label class="field-label">Penerima</label>
            <input name="penerima" required class="field-input">
          </div>
          <div>
            <label class="field-label">Lokasi Tujuan</label>
            <input name="lokasi_tujuan" class="field-input">
          </div>
          <div class="md:col-span-2">
            <label class="field-label">Catatan</label>
            <textarea name="catatan" rows="2" class="field-input"></textarea>
          </div>
        </div>
        <button type="submit" class="mt-6 rounded-xl bg-brand-emerald px-5 py-3 text-sm font-black text-white transition hover:bg-brand-slate">
          Simpan Transaksi Keluar
        </button>
      </form>
    </section>
  </main>
</div>

<style>
  .field-label { display: block; margin-bottom: 0.5rem; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: var(--brand-slate, #475569); }
  .field-input { width: 100%; border-radius: 0.75rem; border: 1px solid var(--brand-accent, #dbe5df); background-color: #f7faf8; padding: 0.75rem 1rem; font-size: 0.875rem; }
  .field-input:focus { border-color: var(--brand-emerald, #087443); outline: 2px solid transparent; box-shadow: 0 0 0 2px rgba(8, 116, 67, 0.2); }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
  const buttons = document.querySelectorAll('.oneshot-tab-btn');
  const panels = document.querySelectorAll('.oneshot-tab-content');

  // Tab switching engine
  const activate = (tab) => {
    buttons.forEach((button) => {
      const active = button.dataset.tab === tab;
      button.classList.toggle('border-emerald-600', active);
      button.classList.toggle('text-emerald-700', active);
      button.classList.toggle('bg-emerald-50', active);
      button.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    panels.forEach((panel) => panel.classList.toggle('hidden', panel.id !== `tab-content-${tab}`));
  };

  buttons.forEach((button) => button.addEventListener('click', () => activate(button.dataset.tab)));

  // Toast notification launcher
  const toast = (message, type = 'success') => {
    const node = document.createElement('div');
    node.className = `fixed bottom-5 right-5 z-50 rounded-xl px-5 py-3 text-sm font-bold shadow-2xl transition-all duration-300 ${type === 'success' ? 'bg-slate-900 text-emerald-300' : 'bg-rose-900 text-rose-100'}`;
    node.textContent = message;
    document.body.appendChild(node);
    setTimeout(() => node.remove(), 3200);
  };

  // Hybrid Dropdown Engine (Pilih / Ketik Langsung)
  function initHybridDropdowns() {
    document.querySelectorAll('.hybrid-select').forEach((wrapper) => {
      const input = wrapper.querySelector('.hybrid-input');
      const toggleBtn = wrapper.querySelector('.hybrid-toggle');
      const list = wrapper.querySelector('.hybrid-options');

      if (!input || !list) return;

      const openOptions = () => {
        document.querySelectorAll('.hybrid-options').forEach((el) => {
          if (el !== list) el.classList.add('hidden');
        });
        filterOptions();
      };

      const closeOptions = () => list.classList.add('hidden');

      const filterOptions = () => {
        const val = input.value.toLowerCase().trim();
        let hasVisible = false;
        list.querySelectorAll('.hybrid-option').forEach((opt) => {
          const text = opt.textContent.toLowerCase();
          if (text.includes(val)) {
            opt.classList.remove('hidden');
            hasVisible = true;
          } else {
            opt.classList.add('hidden');
          }
        });
        if (hasVisible) {
          list.classList.remove('hidden');
        } else {
          list.classList.add('hidden');
        }
      };

      input.addEventListener('focus', openOptions);
      input.addEventListener('input', () => {
        list.classList.remove('hidden');
        filterOptions();
      });

      toggleBtn?.addEventListener('click', (e) => {
        e.stopPropagation();
        if (list.classList.contains('hidden')) {
          openOptions();
        } else {
          closeOptions();
        }
      });

      list.addEventListener('click', (e) => {
        const option = e.target.closest('.hybrid-option');
        if (option) {
          input.value = option.dataset.value || option.textContent.trim();
          closeOptions();
        }
      });
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('.hybrid-select')) {
        document.querySelectorAll('.hybrid-options').forEach((el) => el.classList.add('hidden'));
      }
    });
  }

  initHybridDropdowns();

  // Generic AJAX for Micro Master Forms
  document.querySelectorAll('.ajax-micro-form').forEach((form) => {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      try {
        const response = await fetch(form.action, {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
          body: new FormData(form)
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'Data gagal disimpan.');
        
        form.reset();
        toast('Data master berhasil ditambahkan.');
        await refreshDropdowns();
      } catch (error) {
        toast(error.message || 'Terjadi kesalahan jaringan.', 'error');
      }
    });
  });

  // Dynamic Refresh for Dropdowns & Hybrid Options
  async function refreshDropdowns() {
    try {
      const response = await fetch('{{ route("form.options") }}', { headers: { Accept: 'application/json' } });
      const data = await response.json();
      if (!data.success) return;

      // Update Standard Dropdowns
      const groups = [
        ['.select-pemasok', data.pemasoks],
        ['.select-lokasi', data.lokasis],
        ['.select-kondisi', data.kondisis]
      ];

      groups.forEach(([selector, items]) => {
        document.querySelectorAll(selector).forEach((select) => {
          const value = select.value;
          select.innerHTML = '<option value="">-- Pilih --</option>';
          (items || []).forEach((item) => {
            const option = new Option(item.nama, item.id);
            option.selected = String(item.id) === String(value);
            select.add(option);
          });
        });
      });

      // Update Item Select Dropdowns
      document.querySelectorAll('.select-item-kode').forEach((select) => {
        const value = select.value;
        select.innerHTML = '<option value="">-- Pilih Barang --</option>';
        (data.items || []).forEach((item) => {
          const option = new Option(`${item.kode_barang} — ${item.nama_barang}`, item.kode_barang);
          option.selected = item.kode_barang === value;
          select.add(option);
        });
      });

      // Update Hybrid Options List (Kategori & Satuan)
      const updateHybridOptions = (wrapperId, items) => {
        const wrapper = document.getElementById(wrapperId);
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

      updateHybridOptions('hybrid-kategori', data.kategories);
      updateHybridOptions('hybrid-satuan', data.satuans);

    } catch {
      /* Keep existing dropdown options if network refresh fails */
    }
  }

  // Live Stock Lookup on Outbound Select
  document.querySelector('#select-item-outbound')?.addEventListener('change', async (event) => {
    const badge = document.querySelector('#live-stock-badge');
    const code = event.target.value;
    if (!code) { badge.textContent = ''; return; }

    try {
      const response = await fetch(`/api/form/item-detail/${encodeURIComponent(code)}`, { headers: { Accept: 'application/json' } });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error();

      const item = result.data;
      document.querySelector('#input-harga-jual').value = item.harga_dasar ?? '';
      badge.className = `rounded-full px-3 py-1 text-xs font-black ${item.stok > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'}`;
      badge.textContent = item.stok > 0 ? `Sisa Stok: ${item.stok}` : 'Stok Habis (0)';
    } catch {
      badge.textContent = 'Stok tidak dapat dimuat';
      badge.className = 'text-xs font-bold text-rose-600';
    }
  });
});
</script>
@endsection
@extends('layouts.bakoelkembang')

@section('content')
<!-- PUBLIC E-CATALOG VIEW -->
<div class="flex-grow max-w-7xl w-full mx-auto p-6 md:p-8 space-y-8 animate-fade-in">
  
  <!-- Banner Botanical Store -->
  <div class="bg-gradient-to-r from-brand-emerald via-[#083A27] to-[#042418] rounded-[32px] p-8 md:p-12 text-white relative overflow-hidden shadow-xl border border-white/5">
    <div class="absolute -right-16 -bottom-16 w-80 h-80 bg-brand-sage/20 rounded-full blur-3xl"></div>
    <div class="max-w-2xl space-y-4 relative z-10">
      <span class="bg-white/15 text-brand-sage font-black text-xs px-4 py-1.5 rounded-full border border-white/10 uppercase tracking-widest inline-block">
        🌸 Premium Botanical Store & Nursery
      </span>
      <h2 class="text-3xl md:text-5xl font-black leading-tight tracking-tight">
        Koleksi Anggrek Hibrid & Spesies Kebun
      </h2>
      <p class="text-lg text-brand-accent font-medium leading-relaxed">
        Flora premium terbaik yang dibudidayakan secara eksklusif. Sehat, bebas hama, dan bergaransi mekar indah menghiasi hunian Anda.
      </p>
      <div class="flex flex-wrap gap-4 pt-2">
        <a href="#etalase" class="px-8 py-4 bg-brand-sage hover:bg-opacity-90 text-brand-emerald font-black text-md rounded-2xl shadow-lg transition-all inline-flex items-center gap-2">
          <i class="fas fa-seedling"></i> LIHAT KATALOG BUNGA
        </a>
        <a href="{{ route('profil') }}" class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white border border-white/20 font-extrabold text-md rounded-2xl transition-all inline-flex items-center gap-2">
          PROFIL KEBUN
        </a>
      </div>
    </div>
  </div>

  <!-- Role Cards Quick Access -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl p-6 border border-brand-accent shadow-sm hover:shadow-md transition-shadow">
      <div class="w-12 h-12 bg-emerald-50 text-brand-emerald rounded-2xl flex items-center justify-center text-2xl font-black mb-4">
        👑
      </div>
      <h3 class="text-xl font-black text-brand-emerald mb-2">Superadmin</h3>
      <p class="text-sm text-brand-slate font-medium mb-4">Kelola akun pengguna, hak akses multi-tenant, serta audit rincian sistem.</p>
      <a href="{{ route('superadmin') }}" class="text-xs font-black text-brand-emerald hover:underline inline-flex items-center gap-1">LIHAT DETAIL &rarr;</a>
    </div>

    <div class="bg-white rounded-3xl p-6 border border-brand-accent shadow-sm hover:shadow-md transition-shadow">
      <div class="w-12 h-12 bg-emerald-50 text-brand-emerald rounded-2xl flex items-center justify-center text-2xl font-black mb-4">
        🏡
      </div>
      <h3 class="text-xl font-black text-brand-emerald mb-2">Gudang & Kebun</h3>
      <p class="text-sm text-brand-slate font-medium mb-4">Mencatat arus keluar masuk barang, cetak label QR, berita acara, serta laci kas.</p>
      <a href="{{ route('gudang') }}" class="text-xs font-black text-brand-emerald hover:underline inline-flex items-center gap-1">LIHAT DETAIL &rarr;</a>
    </div>

    <div class="bg-white rounded-3xl p-6 border border-brand-accent shadow-sm hover:shadow-md transition-shadow">
      <div class="w-12 h-12 bg-emerald-50 text-brand-emerald rounded-2xl flex items-center justify-center text-2xl font-black mb-4">
        📊
      </div>
      <h3 class="text-xl font-black text-brand-emerald mb-2">Viewer Logistik</h3>
      <p class="text-sm text-brand-slate font-medium mb-4">Akses cepat laporan stok dan analisis arus tanpa merubah data fisik.</p>
      <a href="{{ route('viewer') }}" class="text-xs font-black text-brand-emerald hover:underline inline-flex items-center gap-1">LIHAT DETAIL &rarr;</a>
    </div>
  </div>

  <!-- Etalase Bunga -->
  <div id="etalase" class="space-y-6 pt-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h3 class="text-2xl md:text-3xl font-black text-brand-emerald tracking-tight">Etalase Bunga Siap Kirim</h3>
        <p class="text-brand-slate text-sm font-medium">Beli langsung dari kebun kami via jalur WhatsApp Instan sekali klik.</p>
      </div>
      <div class="relative w-full sm:w-80">
        <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-brand-sage"></i>
        <input type="text" id="katalog-search" oninput="filterKatalog(this.value)" placeholder="Cari nama anggrek..." class="w-full pl-12 pr-6 py-3 rounded-full border border-brand-accent bg-white shadow-sm text-md font-bold focus:outline-none focus:border-brand-emerald">
      </div>
    </div>

    <!-- Katalog Grid -->
    <div id="katalog-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Item Cards -->
    </div>
  </div>
</div>

@push('scripts')
<script>
  const sampleKatalog = [
    {
      id: "1005",
      nama: "Cattleya Mantinii",
      harga: 230000,
      kategori: "Hibrid",
      satuan: "pot",
      catatan: "Bunga berwarna ungu fuchsia dengan aroma harum manis khas orchidaceae.",
      foto: "https://images.unsplash.com/photo-1525310072745-f49212b5ac6d?auto=format&fit=crop&w=600&q=80"
    },
    {
      id: "1006",
      nama: "Cymbidium Golden Boy",
      harga: 172500,
      kategori: "Hibrid",
      satuan: "pot",
      catatan: "Anggrek kuning keemasan yang melambangkan kemakmuran dan keberuntungan.",
      foto: "https://images.unsplash.com/photo-1453904300235-0f2f60b15b5d?auto=format&fit=crop&w=600&q=80"
    },
    {
      id: "1007",
      nama: "Dendrobium Burana Jade Fancy",
      harga: 276000,
      kategori: "Hibrid",
      satuan: "pot",
      catatan: "Kombinasi warna hijau giok dengan percikan merah anggun di bagian labellum.",
      foto: "https://images.unsplash.com/photo-1508780709619-79562169bc64?auto=format&fit=crop&w=600&q=80"
    },
    {
      id: "1008",
      nama: "Phalaenopsis Amabilis",
      harga: 115000,
      kategori: "Spesies",
      satuan: "pot",
      catatan: "Anggrek Bulan Putih khas Indonesia. Puspa Pesona yang anggun dan menawan.",
      foto: "https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?auto=format&fit=crop&w=600&q=80"
    }
  ];

  function renderKatalog(list) {
    const grid = document.getElementById("katalog-grid");
    if (!grid) return;
    grid.innerHTML = "";

    list.forEach(item => {
      const priceStr = item.harga.toLocaleString("id-ID");
      const card = `
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm border border-brand-accent hover:border-brand-emerald/40 transition-all group duration-300">
          <div class="h-56 bg-brand-sage/10 relative overflow-hidden">
            <img src="${item.foto}" alt="${item.nama}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" referrerpolicy="no-referrer">
            <div class="absolute top-3 right-3 bg-white/95 backdrop-blur px-2.5 py-0.5 rounded-lg text-[9px] font-black text-brand-emerald border border-brand-accent uppercase tracking-wider">
              ${item.kategori}
            </div>
          </div>
          <div class="p-6 space-y-4">
            <div>
              <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block font-mono">SKU-${item.id} • Satuan: ${item.satuan}</span>
              <h4 class="text-xl font-bold text-gray-900 group-hover:text-brand-emerald transition-colors line-clamp-1 mt-1">${item.nama}</h4>
              <p class="text-xs text-brand-slate line-clamp-2 mt-1 font-medium">${item.catatan}</p>
            </div>
            <div class="flex flex-col gap-3 pt-3 border-t border-gray-100">
              <div class="flex justify-between items-center">
                <span class="text-xs font-bold text-gray-400 uppercase">Harga:</span>
                <span class="text-xl font-black text-brand-emerald">Rp ${priceStr}</span>
              </div>
              <button onclick="triggerWhatsappBeli('${item.nama}', ${item.harga})" class="w-full bg-[#25D366] hover:bg-[#20ba56] text-white py-3 rounded-xl font-black text-xs flex items-center justify-center gap-2 shadow-sm shadow-green-200 cursor-pointer">
                <i class="fab fa-whatsapp text-lg"></i> 1-CLICK SHARE & TANYA WA
              </button>
            </div>
          </div>
        </div>
      `;
      grid.innerHTML += card;
    });
  }

  function filterKatalog(query) {
    const q = query.toLowerCase();
    const filtered = sampleKatalog.filter(i => i.nama.toLowerCase().includes(q) || i.kategori.toLowerCase().includes(q));
    renderKatalog(filtered);
  }

  function triggerWhatsappBeli(nama, harga) {
    const hargaText = harga.toLocaleString("id-ID");
    const text = `Halo Bakoelkembang, saya tertarik membeli anggrek *${nama}* dengan harga *Rp ${hargaText}*. Apakah stok masih tersedia?`;
    window.open(`https://wa.me/6282229225456?text=${encodeURIComponent(text)}`, "_blank");
    triggerToast("✓ PESANAN DI-SHARE!", "Simulasi detail pemesanan anggrek diteruskan ke Whatsapp.");
  }

  document.addEventListener("DOMContentLoaded", function() {
    renderKatalog(sampleKatalog);
  });
</script>
@endpush
@endsection
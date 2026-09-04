@extends('layouts.bakoelkembang')

@section('content')
<!-- PUBLIC E-CATALOG VIEW -->
<div class="flex-grow max-w-7xl w-full mx-auto p-4 md:p-8 space-y-8 animate-fade-in">

  <!-- Banner Botanical Store -->
  <div class="bg-gradient-to-br from-brand-emerald via-[#083A27] to-[#042418] rounded-[32px] p-6 md:p-12 text-white relative overflow-hidden shadow-2xl border border-white/10">
    <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-brand-sage/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="max-w-2xl space-y-4 relative z-10">
      <span class="bg-white/15 backdrop-blur-md text-brand-sage font-black text-[11px] px-4 py-1.5 rounded-full border border-white/10 uppercase tracking-widest inline-flex items-center gap-2">
        <i class="fas fa-seedling text-emerald-400"></i> Premium Botanical Store
      </span>
      <h2 class="text-3xl md:text-5xl font-black leading-tight tracking-tight">
        Koleksi Anggrek Hibrid & Spesies Kebun
      </h2>
      <p class="text-sm md:text-base text-brand-accent/90 font-medium leading-relaxed">
        Flora premium terbaik yang dibudidayakan secara eksklusif. Sehat, bebas hama, dan bergaransi mekar indah menghiasi hunian Anda.
      </p>
    </div>
  </div>

  <!-- Control & Filter Bar -->
  <div class="space-y-6">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 bg-white p-5 rounded-3xl border border-gray-100 shadow-sm">
      <div class="space-y-1">
        <h3 class="text-xl md:text-2xl font-black text-brand-emerald tracking-tight">Etalase Bunga Siap Kirim</h3>
        <p class="text-brand-slate text-xs md:text-sm font-medium">Beli langsung dari kebun kami via WhatsApp sekali klik.</p>
      </div>

      <div class="flex flex-col sm:flex-row w-full lg:w-auto items-center gap-3">
        <!-- Search Input -->
        <div class="relative w-full sm:w-72">
          <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
          <input type="text" id="katalog-search" oninput="applyFilters()" placeholder="Cari nama, genus, indukan..." class="w-full pl-11 pr-4 py-2.5 rounded-2xl border border-gray-200 bg-gray-50/50 text-sm font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:border-brand-emerald focus:bg-white transition-all">
        </div>

        <!-- Genus Filter -->
        <div class="relative w-full sm:w-44">
          <select id="katalog-genus" onchange="applyFilters()" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 bg-gray-50/50 text-sm font-semibold text-gray-700 focus:outline-none focus:border-brand-emerald focus:bg-white transition-all appearance-none cursor-pointer">
            <option value="all">Semua Genus</option>
          </select>
          <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>

        <!-- Sort Select -->
        <div class="relative w-full sm:w-48">
          <select id="katalog-sort" onchange="applyFilters()" class="w-full px-4 py-2.5 rounded-2xl border border-gray-200 bg-gray-50/50 text-sm font-semibold text-gray-700 focus:outline-none focus:border-brand-emerald focus:bg-white transition-all appearance-none cursor-pointer">
            <option value="default">Urutkan: Terbaru</option>
            <option value="name-asc">Nama: A - Z</option>
          </select>
          <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>
      </div>
    </div>

    <!-- Filter Category Pills -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none" id="category-pills">
      <button onclick="filterCategory('all')" data-cat="all" class="cat-pill active px-5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-brand-emerald text-white shadow-md shadow-brand-emerald/20">
        Semua Koleksi
      </button>
      <button onclick="filterCategory('Hibrid')" data-cat="Hibrid" class="cat-pill px-5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-gray-600 border border-gray-200 hover:border-brand-emerald">
        Anggrek Hibrid
      </button>
      <button onclick="filterCategory('Spesies')" data-cat="Spesies" class="cat-pill px-5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-gray-600 border border-gray-200 hover:border-brand-emerald">
        Anggrek Spesies
      </button>
    </div>

    <!-- Katalog Grid -->
    <div id="katalog-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"></div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden text-center py-16 space-y-3 bg-white rounded-3xl border border-dashed border-gray-200">
      <div class="w-16 h-16 bg-emerald-50 text-brand-emerald rounded-full flex items-center justify-center mx-auto text-2xl">
        <i class="fas fa-leaf"></i>
      </div>
      <h4 class="text-lg font-bold text-gray-800">Bunga tidak ditemukan</h4>
      <p class="text-xs text-gray-500 max-w-sm mx-auto">Coba gunakan kata kunci lain atau pilih filter yang berbeda.</p>
    </div>
  </div>
</div>

<!-- MODAL DETAIL BUNGA -->
<div id="modal-detail" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4 overflow-y-auto transition-opacity duration-300">
  <div class="bg-white rounded-3xl max-w-3xl w-full overflow-hidden shadow-2xl border border-gray-100 transform transition-all relative my-8">
    <!-- Close Button -->
    <button onclick="closeModalDetail()" class="absolute top-4 right-4 z-20 w-10 h-10 bg-white/80 hover:bg-white text-gray-700 rounded-full flex items-center justify-center shadow-md transition-all cursor-pointer">
      <i class="fas fa-times text-base"></i>
    </button>

    <div class="grid grid-cols-1 md:grid-cols-2">
      <!-- Media Gallery Section -->
      <div class="p-6 bg-gray-50/80 flex flex-col justify-between border-b md:border-b-0 md:border-r border-gray-100">
        <div class="space-y-3">
          <!-- Main Image Display -->
          <div class="relative h-64 md:h-80 rounded-2xl overflow-hidden shadow-inner bg-gray-100 flex items-center justify-center">
            <img id="modal-main-img" src="" alt="" class="w-full h-full object-cover transition-all duration-300" onerror="handleImageError(this)">
            <span id="modal-kategori-badge" class="absolute top-3 left-3 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-[10px] font-black text-brand-emerald border border-brand-accent uppercase tracking-wider shadow-sm">
            </span>
          </div>

          <!-- Thumbnails Strip (Dynamic) -->
          <div id="modal-thumbnails" class="flex gap-2 overflow-x-auto pb-1"></div>
        </div>
        <p class="text-[11px] text-gray-400 text-center font-medium mt-3">
          <i class="fas fa-info-circle mr-1"></i> Klik foto kecil untuk memperbesar
        </p>
      </div>

      <!-- Detail Info Section -->
      <div class="p-6 md:p-8 flex flex-col justify-between space-y-6">
        <div class="space-y-4">
          <div>
            <div class="flex items-center gap-2 mb-1">
              <span id="modal-sku" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-mono"></span>
              <span id="modal-genus-tag" class="text-[10px] font-extrabold bg-emerald-100 text-brand-emerald px-2 py-0.5 rounded-md"></span>
            </div>
            <h3 id="modal-nama" class="text-2xl font-black text-gray-900 leading-snug"></h3>
            <p id="modal-indukan-wrap" class="hidden text-xs text-brand-emerald font-semibold mt-1">
              <i class="fas fa-dna mr-1"></i> Indukan: <span id="modal-indukan" class="text-gray-700 font-normal"></span>
            </p>
          </div>

          <div class="bg-emerald-50/60 border border-emerald-100 p-4 rounded-2xl flex justify-between items-center">
            <div>
            </div>
            <span id="modal-satuan" class="text-xs font-bold bg-white text-emerald-800 px-3 py-1.5 rounded-xl shadow-sm border border-emerald-100">
            </span>
          </div>

          <div class="space-y-2">
            <h5 class="text-xs font-bold uppercase tracking-wider text-gray-400">Deskripsi / Spesifikasi</h5>
            <p id="modal-catatan" class="text-xs text-gray-600 leading-relaxed font-medium bg-gray-50 p-3.5 rounded-2xl border border-gray-100">
            </p>
          </div>
        </div>

        <div class="space-y-3 pt-4 border-t border-gray-100">
          <button id="modal-wa-btn" class="h text-white py-3.5 rounded-2xl font-black text-xs flex items-center justify-center gap-2.5 shadow-lg shadow-green-600/20 transition-all cursor-pointer">
          </button>
          <button onclick="closeModalDetail()" class="w-full bg-gray-100 hover:bg-gray-200 text-gray-600 py-2.5 rounded-xl font-bold text-xs transition-all cursor-pointer">
            Tutup Preview
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // Dynamic Placeholder Anggrek SVG Data URI
  const orchidPlaceholder = `data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600" viewBox="0 0 24 24" fill="none" stroke="%23059669" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" style="background:%23f0fdf4;"><rect width="100%" height="100%" fill="%23f0fdf4"/><circle cx="12" cy="12" r="3" fill="%2310b981"/><path d="M12 9C10.5 5 7 4 7 7c0 2 2.5 4 5 5m0-3c1.5-4 5-5 5-2 0 2-2.5 4-5 5"/><path d="M9 12c-4 1.5-5 5-2 5 2 0 4-2.5 5-5m-3 0c-4-1.5-5-5-2-5 2 0 4 2.5 5 5"/><path d="M12 15c-1.5 4-5 5-5 2 0-2 2.5-4 5-5m0 3c1.5 4 5 5 5 2 0-2-2.5-4-5-5"/><text x="50%" y="82%" font-family="sans-serif" font-size="1.8" font-weight="bold" fill="%23047857" text-anchor="middle">Anggrek Bakoelkembang</text></svg>`;

  function handleImageError(img) {
    img.onerror = null;
    img.src = orchidPlaceholder;
  }

  // Raw dataset dinamis yang disesuaikan dari data.csv (Dapat di-override via $katalogs Controller)
  const defaultKatalogData = [
    {
      "id": "452",
      "nama": "Lady of the Night",
      "harga_dasar": 75000,
      "kategori": "Spesies",
      "genus": "Brassavola",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Brassavola nodosa - Lady of the Night",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000024208_000027911-14.jpg",
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000098724_000024208.jpg",
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000024208_000022558.jpeg"
      ]
    },
    {
      "id": "453",
      "nama": "Anggrek Hitam Papua",
      "harga_dasar": 85000,
      "kategori": "Spesies",
      "genus": "Bulbophyllum",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Bulbophyllum beccarii - Anggrek Hitam Papua",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000189185_000025255-edited.jpg",
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000025255_000026312.jpg"
      ]
    },
    {
      "id": "457",
      "nama": "Anggrek Hitam",
      "harga_dasar": 90000,
      "kategori": "Spesies",
      "genus": "Coelogyne",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Coelogyne pandurata - Anggrek Hitam Kalimantan",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000045191_000000798-6.jpeg",
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000045191_000026851-scaled.jpeg"
      ]
    },
    {
      "id": "461",
      "nama": "Coelogyne Dayana",
      "harga_dasar": 80000,
      "kategori": "Spesies",
      "genus": "Coelogyne",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Coelogyne pulverula - Coelogyne Dayana",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000187643_000045224-4.jpg"
      ]
    },
    {
      "id": "462",
      "nama": "Anggrek Mutiara",
      "harga_dasar": 75000,
      "kategori": "Spesies",
      "genus": "Coelogyne",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Coelogyne asperata - Anggrek Mutiara",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000075265_000044945-4.jpg",
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000192563_000044945.jpg"
      ]
    },
    {
      "id": "469",
      "nama": "Anggrek Antena",
      "harga_dasar": 95000,
      "kategori": "Spesies",
      "genus": "Dendrobium",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Dendrobium antennatum - Anggrek Antena",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000052718_000057079.jpg"
      ]
    },
    {
      "id": "474",
      "nama": "Anggrek Besi",
      "harga_dasar": 120000,
      "kategori": "Spesies",
      "genus": "Dendrobium",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Dendrobium violaceoflavens - Anggrek Besi Papua",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000059460_000024020-1-1.jpg"
      ]
    },
    {
      "id": "479",
      "nama": "Anggrek Tirai",
      "harga_dasar": 65000,
      "kategori": "Spesies",
      "genus": "Dendrobium",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Dendrobium aphyllum - Anggrek Tirai Menjuntai",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000188677_000057084.jpg"
      ]
    },
    {
      "id": "485",
      "nama": "Anggrek Sikat",
      "harga_dasar": 70000,
      "kategori": "Spesies",
      "genus": "Dendrobium",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Dendrobium secundum - Anggrek Sikat Merah/Pink",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000041049_000059057-1-1.jpg"
      ]
    },
    {
      "id": "491",
      "nama": "Anggrek Macan",
      "harga_dasar": 110000,
      "kategori": "Spesies",
      "genus": "Grammatophyllum",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Grammatophyllum scriptum - Anggrek Macan",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000041270_000092288-3.jpg"
      ]
    },
    {
      "id": "497",
      "nama": "Anggrek Hitam Sulawesi",
      "harga_dasar": 125000,
      "kategori": "Spesies",
      "genus": "Grammatophyllum",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Grammatophyllum stapeliiflorum - Anggrek Hitam Sulawesi",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000044620_000092296.jpg"
      ]
    },
    {
      "id": "503",
      "nama": "Anggrek Kelapa",
      "harga_dasar": 60000,
      "kategori": "Spesies",
      "genus": "Maxillaria",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Maxillaria tenuifolia - Anggrek Aroma Kelapa/Santan",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000011762_000123026.jpg"
      ]
    },
    {
      "id": "509",
      "nama": "Vanda Douglas - Papilionanthe teres",
      "harga_dasar": 55000,
      "kategori": "Spesies",
      "genus": "Papilionanthe",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Papilionanthe teres - Vanda Douglas",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000049114_000147347.jpg"
      ]
    },
    {
      "id": "516",
      "nama": "Anggrek Ekor Tikus sp Kalimantan / Capung Jawa",
      "harga_dasar": 135000,
      "kategori": "Spesies",
      "genus": "Paraphalaenopsis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Paraphalaenopsis labukensis - Anggrek Ekor Tikus",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000018360_000007344.jpg"
      ]
    },
    {
      "id": "523",
      "nama": "Anggrek Bulan Sp Kalimantan",
      "harga_dasar": 115000,
      "kategori": "Spesies",
      "genus": "Phalaenopsis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Phalaenopsis bellina - Anggrek Bulan Harum Kalimantan",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000050552_000151157.jpg"
      ]
    },
    {
      "id": "530",
      "nama": "Anggrek Kelip",
      "harga_dasar": 120000,
      "kategori": "Spesies",
      "genus": "Phalaenopsis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Phalaenopsis violacea - Anggrek Kelip",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000036918_000151318.jpg"
      ]
    },
    {
      "id": "537",
      "nama": "Anggrek Bulan Puspa Pesona",
      "harga_dasar": 65000,
      "kategori": "Spesies",
      "genus": "Phalaenopsis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Phalaenopsis amabilis - Anggrek Bulan Putih",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000048077_000151132.jpg"
      ]
    },
    {
      "id": "547",
      "nama": "Anggrek Bulan Seleriana",
      "harga_dasar": 130000,
      "kategori": "Spesies",
      "genus": "Phalaenopsis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Phalaenopsis schilleriana - Anggrek Bulan Motif Daun Macan",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000054594_000151291.jpg"
      ]
    },
    {
      "id": "555",
      "nama": "Anggrek Bulan Raksasa",
      "harga_dasar": 250000,
      "kategori": "Spesies",
      "genus": "Phalaenopsis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Phalaenopsis Gigantea - Anggrek Bulan Raksasa Kalimantan",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000074534_000151213.jpg"
      ]
    },
    {
      "id": "563",
      "nama": "Phalaenopsis modesta",
      "harga_dasar": 85000,
      "kategori": "Spesies",
      "genus": "Phalaenopsis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Phalaenopsis modesta Spesies Kalimantan",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000051742_000151259.jpg"
      ]
    },
    {
      "id": "571",
      "nama": "Anggrek Tanduk Rusa",
      "harga_dasar": 75000,
      "kategori": "Spesies",
      "genus": "Phalaenopsis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Phalaenopsis cornu-cervi - Anggrek Tanduk Rusa",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000121735_000151175.jpg"
      ]
    },
    {
      "id": "580",
      "nama": "Anggrek Bintang",
      "harga_dasar": 60000,
      "kategori": "Spesies",
      "genus": "Pomatocalpa",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Pomatocalpa latifolium - Anggrek Bintang",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/Cleisostoma-latifolium-Lindl.-1.jpg"
      ]
    },
    {
      "id": "586",
      "nama": "Anggrek Ekor Tupai",
      "harga_dasar": 90000,
      "kategori": "Spesies",
      "genus": "Rhynchostylis",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Rhynchostylis gigantea - Anggrek Ekor Tupai Wangi",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000047398_000178381.jpg"
      ]
    },
    {
      "id": "594",
      "nama": "Vanda dearei",
      "harga_dasar": 140000,
      "kategori": "Spesies",
      "genus": "Vanda",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Vanda dearei Spesies Kuning Harum khas Borneo",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/spc_000188400_000211454.jpg"
      ]
    },
    {
      "id": "603",
      "nama": "Zygopetalum Big Country",
      "harga_dasar": 150000,
      "kategori": "Spesies",
      "genus": "Zygopetalum",
      "indukan": "",
      "satuan": "pot",
      "catatan": "Zygopetalum Big Country Aroma Sangat Harum",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/08/images-2.jpeg"
      ]
    },
    {
      "id": "617",
      "nama": "Cattleya Mantinii",
      "harga_dasar": 100000,
      "kategori": "Hibrid",
      "genus": "Cattleya",
      "indukan": "Cattleya grandis × Cattleya purpurata",
      "satuan": "pot",
      "catatan": "Bunga berwarna ungu fuchsia dengan aroma harum manis khas orchidaceae.",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/2dfDCCVyaLjzSUFB8XCznL_Cattleya_Mantinii.jpg",
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/JKYvB9dnX3fPdmcWG5SsZb_IMG_20250308_050722.jpg"
      ]
    },
    {
      "id": "624",
      "nama": "Cymbidium Golden Boy",
      "harga_dasar": 95000,
      "kategori": "Hibrid",
      "genus": "Cymbidium",
      "indukan": "Cymbidium Pixie Moor × Cymbidium Wallara",
      "satuan": "pot",
      "catatan": "Cymbidium Golden Boy - Hibrid Kuantitas Bunga Banyak Lebat",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/hyb_000422564_100072865.jpg",
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/5b3yV6c3KwQeUcHEsgaCQE_IMG-20250404-WA00371.jpg"
      ]
    },
    {
      "id": "629",
      "nama": "Dendrobium Burana Jade Fancy Splash",
      "harga_dasar": 120000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium Bangkok Green × Dendrobium Burana Fancy",
      "satuan": "pot",
      "catatan": "Dendrobium Burana Jade Fancy Splash",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/Dendrobium_100105798_000005025-scaled.jpg"
      ]
    },
    {
      "id": "636",
      "nama": "Dendrobium Caesar Yellow",
      "harga_dasar": 85000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium bigibbum var. schroederianum × Dendrobium stratiotes",
      "satuan": "pot",
      "catatan": "Dendrobium Caesar Yellow - Anggrek Keriting Kuning",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/hyb_100086975_000008835-scaled.jpg"
      ]
    },
    {
      "id": "647",
      "nama": "Dendrobium Meesangnil",
      "harga_dasar": 90000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium Helen Izuta × Dendrobium Jaquelyn Thomas",
      "satuan": "pot",
      "catatan": "Dendrobium Meesangnil Bunga Ungu Gelap Pekat",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/hyb_100038668_000016955.png"
      ]
    },
    {
      "id": "654",
      "nama": "Dendrobium Carol Goo",
      "harga_dasar": 85000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium Caesar × Dendrobium Janice Tanaka",
      "satuan": "pot",
      "catatan": "Dendrobium Carol Goo Hibrid Keriting Anggun",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/Dendrobium_100058104_000003006.jpg"
      ]
    },
    {
      "id": "661",
      "nama": "Dendrobium Alesa Agustina putri Azzahra",
      "harga_dasar": 110000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium Grímsson-Moussaieff × Dendrobium Najwa Jelita",
      "satuan": "pot",
      "catatan": "Hibrid eksklusif corak warna teratai elegan",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/RpaPegJG6hamgYLUD7sTrL_IMG20250104123159-scaled.jpg"
      ]
    },
    {
      "id": "665",
      "nama": "Dendrobium Caesar Red",
      "harga_dasar": 85000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium Caesar × Dendrobium Tokiko Inaba",
      "satuan": "pot",
      "catatan": "Dendrobium Caesar Red Keriting Merah Tua",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/dWPsQhph2SXQqGCaukHNLZ_1743906578249.jpg"
      ]
    },
    {
      "id": "671",
      "nama": "Dendrobium Bantimurung",
      "harga_dasar": 95000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium Clara Bundt × Dendrobium lasianthera",
      "satuan": "pot",
      "catatan": "Dendrobium Bantimurung Keriting Melintir Lasianthera",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/hyb_000379090_100056930.jpg"
      ]
    },
    {
      "id": "678",
      "nama": "Dendrobium Superbien - Dendrobium Tual Maluku",
      "harga_dasar": 105000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium bigibbum × Dendrobium discolor",
      "satuan": "pot",
      "catatan": "Dendrobium Superbiens Asal Tual Maluku",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/3hCQK2xxcbrFTBpLcVgZSZ_Den_Superbiens.jpg"
      ]
    },
    {
      "id": "688",
      "nama": "Dendrobium Sonia",
      "harga_dasar": 75000,
      "kategori": "Hibrid",
      "genus": "Dendrobium",
      "indukan": "Dendrobium Caesar × Dendrobium Tomie Drake",
      "satuan": "pot",
      "catatan": "Dendrobium Sonia Bunga Ungu Putih Rajin Berbunga",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/hyb_100074545_000029314.jpg"
      ]
    },
    {
      "id": "696",
      "nama": "Oncidium Golden Shower",
      "harga_dasar": 80000,
      "kategori": "Hibrid",
      "genus": "Oncidium",
      "indukan": "Gom. Palolo Gold x Gom. Boissiense",
      "satuan": "pot",
      "catatan": "Oncidium Golden Shower Anggrek Hujan Emas",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/images-4.jpeg"
      ]
    },
    {
      "id": "702",
      "nama": "Phalaenopsis Indonesia Raya",
      "harga_dasar": 130000,
      "kategori": "Hibrid",
      "genus": "Phalaenopsis",
      "indukan": "Phalaenopsis Mad Hatter × Phalaenopsis Tretes Beauty",
      "satuan": "pot",
      "catatan": "Phalaenopsis Indonesia Raya - Anggrek Bulan Hibrid Mewah",
      "foto": [
        "https://e-katalog.bakoelkembang.com/wp-content/uploads/2025/09/spc_000050002_000151132.jpg"
      ]
    }
  ];

  // Integrasi dinamis dari Laravel Controller (jika ada variable $katalogs)
  const katalogData = @json($katalogs ?? null) || defaultKatalogData;

  let activeCategory = 'all';

  function initGenusOptions() {
    const genusSelect = document.getElementById('katalog-genus');
    if (!genusSelect) return;

    const genera = [...new Set(katalogData.map(item => item.genus).filter(Boolean))].sort();

    // Clear and rebuild options
    genusSelect.innerHTML = `<option value="all">Semua Genus</option>`;
    genera.forEach(g => {
      genusSelect.innerHTML += `<option value="${g}">${g}</option>`;
    });
  }

  function renderKatalog(list) {
    const grid = document.getElementById("katalog-grid");
    const emptyState = document.getElementById("empty-state");
    if (!grid) return;

    grid.innerHTML = "";

    if (list.length === 0) {
      emptyState.classList.remove("hidden");
      return;
    } else {
      emptyState.classList.add("hidden");
    }

    list.forEach(item => {
      const calculatedPrice = item.harga_dasar;
      const priceFormatted = calculatedPrice.toLocaleString("id-ID");

      // Validasi foto & photo list
      const photos = Array.isArray(item.foto) && item.foto.length > 0 ? item.foto : [orchidPlaceholder];
      const mainPhoto = photos[0];
      const totalPhotos = photos.length;

      const card = `
        <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 hover:border-brand-emerald/30 transition-all duration-300 group flex flex-col justify-between">
          <div>
            <!-- Image Header -->
            <div class="h-56 bg-gray-50 relative overflow-hidden cursor-pointer flex items-center justify-center" onclick="openModalDetail('${item.id}')">
              <img src="${mainPhoto}" alt="${item.nama}" onerror="handleImageError(this)" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" referrerpolicy="no-referrer">

              <div class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2.5 py-1 rounded-xl text-[10px] font-black text-brand-emerald border border-brand-accent uppercase tracking-wider shadow-sm">
                ${item.kategori}
              </div>

              <div class="absolute top-3 left-3 bg-emerald-950/80 backdrop-blur text-emerald-200 px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase tracking-wider">
                ${item.genus || 'Anggrek'}
              </div>

              ${totalPhotos > 1 ? `
                <div class="absolute bottom-3 right-3 bg-black/60 backdrop-blur text-white px-2.5 py-1 rounded-lg text-[10px] font-bold flex items-center gap-1.5">
                  <i class="fas fa-images"></i> ${totalPhotos} Foto
                </div>
              ` : ''}
            </div>

            <!-- Content -->
            <div class="p-5 space-y-3">
              <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-mono">SKU-${item.id} • Satuan: ${item.satuan}</span>
                <h4 onclick="openModalDetail('${item.id}')" class="text-base font-extrabold text-gray-900 group-hover:text-brand-emerald transition-colors line-clamp-1 mt-0.5 cursor-pointer">${item.nama}</h4>

                ${item.indukan ? `
                  <p class="text-[11px] text-brand-emerald font-semibold line-clamp-1 mt-0.5">
                    <i class="fas fa-dna text-[10px]"></i> ${item.indukan}
                  </p>
                ` : ''}

                <p class="text-xs text-gray-500 line-clamp-2 mt-1 font-medium leading-relaxed">${item.catatan || 'Kultivar rimbun segar tahan lama.'}</p>
              </div>
            </div>
          </div>

          <!-- Bottom Actions -->
          <div class="p-5 pt-0 space-y-3">
            <div class="flex justify-between items-baseline pt-3 border-t border-gray-100">
            </div>

            <div class="grid grid-cols-5 gap-2">
              <button onclick="openModalDetail('${item.id}')" class="col-span-2 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-xl font-bold text-xs transition-all cursor-pointer">
                Detail
              </button>
            </div>
          </div>
        </div>
      `;
      grid.innerHTML += card;
    });
  }

  function filterCategory(cat) {
    activeCategory = cat;

    // UI Active State Tab
    document.querySelectorAll('.cat-pill').forEach(btn => {
      if (btn.dataset.cat === cat) {
        btn.className = "cat-pill active px-5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-brand-emerald text-white shadow-md shadow-brand-emerald/20";
      } else {
        btn.className = "cat-pill px-5 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap bg-white text-gray-600 border border-gray-200 hover:border-brand-emerald";
      }
    });

    applyFilters();
  }

  function applyFilters() {
    const searchVal = document.getElementById("katalog-search").value.toLowerCase();
    const sortVal = document.getElementById("katalog-sort").value;
    const genusVal = document.getElementById("katalog-genus").value;

    let filtered = katalogData.filter(item => {
      const matchCategory = activeCategory === 'all' || item.kategori === activeCategory;
      const matchGenus = genusVal === 'all' || item.genus === genusVal;
      const matchSearch = item.nama.toLowerCase().includes(searchVal) ||
                          (item.genus && item.genus.toLowerCase().includes(searchVal)) ||
                          (item.indukan && item.indukan.toLowerCase().includes(searchVal)) ||
                          item.kategori.toLowerCase().includes(searchVal);
      return matchCategory && matchGenus && matchSearch;
    });

    // Sorting
    if (sortVal === 'price-asc') {
      filtered.sort((a, b) => a.harga_dasar - b.harga_dasar);
    } else if (sortVal === 'price-desc') {
      filtered.sort((a, b) => b.harga_dasar - a.harga_dasar);
    } else if (sortVal === 'name-asc') {
      filtered.sort((a, b) => a.nama.localeCompare(b.nama));
    }

    renderKatalog(filtered);
  }

  // MODAL DETAIL LOGIC
  function openModalDetail(id) {
    const item = katalogData.find(i => String(i.id) === String(id));
    if (!item) return;

    const modal = document.getElementById('modal-detail');
    const mainImg = document.getElementById('modal-main-img');
    const thumbContainer = document.getElementById('modal-thumbnails');

    document.getElementById('modal-sku').innerText = `SKU-${item.id}`;
    document.getElementById('modal-genus-tag').innerText = item.genus || 'ANGGREK';
    document.getElementById('modal-nama').innerText = item.nama;
    document.getElementById('modal-kategori-badge').innerText = item.kategori;
    document.getElementById('modal-satuan').innerText = `Satuan: ${item.satuan}`;
    document.getElementById('modal-catatan').innerText = item.catatan || 'Tidak ada spesifikasi khusus.';

    // Indukan Display
    const indukanWrap = document.getElementById('modal-indukan-wrap');
    if (item.indukan) {
      document.getElementById('modal-indukan').innerText = item.indukan;
      indukanWrap.classList.remove('hidden');
    } else {
      indukanWrap.classList.add('hidden');
    }

    // Set WhatsApp Button Action
    document.getElementById('modal-wa-btn').onclick = () => triggerWhatsappBeli(item.nama, calcPrice);

    // Normalize Images List
    const photos = Array.isArray(item.foto) && item.foto.length > 0 ? item.foto : [orchidPlaceholder];

    // Set Main Image
    mainImg.src = photos[0];
    mainImg.alt = item.nama;

    // Build Thumbnails
    thumbContainer.innerHTML = '';
    photos.forEach((photoUrl, index) => {
      const activeBorder = index === 0 ? 'border-brand-emerald ring-2 ring-brand-emerald/30' : 'border-transparent opacity-60 hover:opacity-100';
      const thumb = document.createElement('img');
      thumb.src = photoUrl;
      thumb.onerror = () => handleImageError(thumb);
      thumb.className = `w-14 h-14 object-cover rounded-xl border-2 cursor-pointer transition-all ${activeBorder}`;
      thumb.onclick = () => {
        mainImg.src = photoUrl;
        Array.from(thumbContainer.children).forEach(child => {
          child.className = 'w-14 h-14 object-cover rounded-xl border-2 border-transparent opacity-60 hover:opacity-100 cursor-pointer transition-all';
        });
        thumb.className = 'w-14 h-14 object-cover rounded-xl border-2 border-brand-emerald ring-2 ring-brand-emerald/30 cursor-pointer transition-all';
      };
      thumbContainer.appendChild(thumb);
    });

    modal.classList.remove('hidden');
    modal.classList.add('flex');
  }

  function closeModalDetail() {
    const modal = document.getElementById('modal-detail');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  document.addEventListener("DOMContentLoaded", function() {
    initGenusOptions();
    renderKatalog(katalogData);
  });
</script>
@endpush
@endsection

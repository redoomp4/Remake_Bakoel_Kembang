<div id="affirmation-toast" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50 {{ session('success') || session('status') || session('error') ? '' : 'hidden' }}">
  <div id="affirmation-content" class="bg-white text-gray-900 border-4 border-brand-emerald p-10 rounded-[32px] shadow-2xl max-w-xl text-center space-y-6 transform scale-100 transition-transform duration-300">
    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center text-brand-emerald mx-auto border border-green-200">
      <i class="fas {{ session('error') ? 'fa-exclamation-triangle text-rose-600' : 'fa-check-circle' }} text-5xl"></i>
    </div>
    <div class="space-y-2">
      <h3 class="text-2xl md:text-3xl font-black text-brand-emerald tracking-tight" id="toast-title">
        {{ session('error') ? '⚠️ PERHATIAN / KENDALA' : '✓ TRANSAKSI & LACI KAS BERHASIL DIPERBARUI!' }}
      </h3>
      <p class="text-md text-brand-slate font-bold" id="toast-msg">
        {{ session('success') ?? session('status') ?? session('error') ?? 'Semua data logistik dan finansial kebun telah di-sync secara aman ke dalam sistem database.' }}
      </p>
    </div>
    <button onclick="closeAffirmationToast()" class="px-8 py-3.5 bg-brand-emerald hover:bg-[#073A27] text-white font-black text-md rounded-2xl shadow-md transition-all">
      Selesai & Tutup
    </button>
  </div>
</div>

<script>
  function closeAffirmationToast() {
    const toast = document.getElementById('affirmation-toast');
    if (toast) toast.classList.add('hidden');
  }

  function triggerToast(title, message) {
    const toast = document.getElementById('affirmation-toast');
    const titleEl = document.getElementById('toast-title');
    const msgEl = document.getElementById('toast-msg');
    if (titleEl) titleEl.innerText = title;
    if (msgEl) msgEl.innerText = message;
    if (toast) toast.classList.remove('hidden');
  }
</script>

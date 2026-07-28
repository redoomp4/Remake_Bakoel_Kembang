<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\MagicLinkController; // ⬅️ pastikan namespace sesuai file kamu
use App\Http\Controllers\Auth\PublicVerifyEmailController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LaporanAsetController;
use App\Http\Controllers\OmzetController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\DashboardGudangController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Magic Link (Guest)
|--------------------------------------------------------------------------
| Form permintaan magic link & endpoint untuk mengirim email magic link.
| Endpoint login via magic link menggunakan signed URL + throttle.
*/
Route::middleware('guest')->group(function () {
    // Form input email untuk minta magic link (opsional)
    Route::get('/magic-link', function () {
        return view('auth.magic-request'); // pastikan view ini ada
    })->name('magic.form');

    // Kirim email magic link (rate-limited)
    Route::post('/magic-link/request', [MagicLinkController::class, 'requestLink'])
        ->middleware('throttle:5,1')   // max 5 permintaan per menit
        ->name('magic.request');
});

// Verifikasi email tanpa auth (AMAN: signed + throttle + cek hash di controller)
Route::get('verify-email/{id}/{hash}', PublicVerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

// Endpoint yang diklik dari email (harus signed)
Route::get('/magic-login', [MagicLinkController::class, 'login'])
    ->middleware(['signed', 'throttle:6,1']) // validasi signature + limit
    ->name('magic.login');

/*
|--------------------------------------------------------------------------
| Public / Landing
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('welcome');
Route::get('/welcome', fn () => view('home'));

Route::get('/profil', fn () => view('landingpage.profil'))->name('profil');
Route::get('/fitur/gudang', fn () => view('landingpage.gudang'))->name('gudang');
Route::get('/fitur/superadmin', fn () => view('landingpage.superadmin'))->name('superadmin');
Route::get('/fitur/viewer', fn () => view('landingpage.viewer'))->name('viewer');
Route::get('/faq', fn () => view('landingpage.faq'))->name('faq');
Route::get('/galeri', fn () => view('landingpage.galeri'))->name('galeri');
Route::get('/kontak', fn () => view('landingpage.kontak'))->name('kontak');

Route::get('/learn-more', fn () => view('learn-more'))->name('learn.more');

Route::get('/barang-masuk/qr/{id}', [BarangMasukController::class, 'qrShow'])->name('barang-masuk.qrshow');
Route::get('/qr/{kode_barang}', [BarangMasukController::class, 'qrShowByKode'])
    ->name('barang-masuk.qrshow.kode');

/*
|--------------------------------------------------------------------------
| Dashboard (global)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', fn () => redirect(App\Providers\RouteServiceProvider::redirectByRole()))
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Protected (auth + verified + auto.logout)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'auto.logout'])->group(function () {

    // Dashboard per role
    Route::get('/dashboard/superadmin', [UserController::class, 'dashboardSuperadmin'])->name('dashboard.superadmin');
    Route::get('/dashboard/gudang', [DashboardGudangController::class, 'index'])->name('dashboard.gudang');
    Route::get('/dashboard/viewer', fn () => view('dashboard.viewer'))->name('dashboard.viewer');

    // Notifikasi (contoh: untuk gudang)
    Route::middleware(['role:gudang'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markSingleRead'])->name('notifications.markSingleRead');
    });

    /*
    |--------------------------------------------------------------------------
    | AJAX / Helper untuk Barang Keluar (gudang only)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:gudang'])->group(function () {
        Route::get('/stok-terpakai', [BarangKeluarController::class, 'cekStok'])->name('barang-keluar.cekStok');
        Route::get('/barang-keluar/detail-barang', [BarangKeluarController::class, 'getDetailBarang'])->name('barang-keluar.detail-barang');
        Route::get('/barang-keluar/barang-bersisa', [BarangKeluarController::class, 'getBarangBersisa'])->name('barang-keluar.barang-bersisa');
        Route::get('/barang-keluar/pilihan-barang', [BarangKeluarController::class, 'getPilihanBarangUnik'])->name('barang-keluar.pilihan-barang');

        // Triple Dropdown AJAX
        Route::get('/barang-keluar/lokasi-by-kode', [BarangKeluarController::class, 'getLokasiByKode'])->name('barang-keluar.lokasi-by-kode');
        Route::get('/barang-keluar/kondisi-by-kode-lokasi', [BarangKeluarController::class, 'getKondisiByKodeLokasi'])->name('barang-keluar.kondisi-by-kode-lokasi');
    });

    /*
    |--------------------------------------------------------------------------
    | Laporan & Export
    |--------------------------------------------------------------------------
    */
    Route::get('/laporan/arus-barang', [LaporanController::class, 'arus'])->name('laporan.arus');
    
    Route::get('/laporan', [ExportController::class, 'laporan'])->name('laporan');

    Route::get('/laporan/print/pdf', [ExportController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/print/excel', [ExportController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('/laporan/arus/pdf', [ExportController::class, 'exportArusPdf'])->name('laporan.arus.pdf');
    Route::get('/laporan/arus/excel', [ExportController::class, 'exportArusExcel'])->name('laporan.arus.excel');

    Route::get('/laporan/aset', [LaporanAsetController::class, 'index'])->name('laporan.aset');
    Route::get('/export-aset-excel', [ExportController::class, 'exportAsetExcel'])->name('export.aset.excel');
    Route::get('/export-aset-pdf', [ExportController::class, 'exportAsetPdf'])->name('export.aset.pdf');

    Route::get('/export/omzet/pdf', [ExportController::class, 'exportOmzetPdf'])->name('export.omzet.pdf');
    Route::get('/export/omzet/excel', [ExportController::class, 'exportOmzetExcel'])->name('export.omzet.excel');

    /*
    |--------------------------------------------------------------------------
    | Resource per Role
    |--------------------------------------------------------------------------
    */

    // GUDANG ONLY
    Route::middleware(['role:gudang'])->group(function () {
        // Master data 
        Route::resource('pemasok', PemasokController::class);
        Route::resource('kondisi', KondisiController::class);
        Route::resource('satuan', SatuanController::class);
        Route::resource('item', ItemController::class);
        Route::resource('lokasi', LokasiController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('aset', LaporanAsetController::class);
        Route::resource('omzet', OmzetController::class);
        Route::resource('barang-masuk', BarangMasukController::class);
        Route::resource('barang-keluar', BarangKeluarController::class);
        
        // Cetak / detail khusus
        Route::get('/barang-masuk/{id}/qr-card', [BarangMasukController::class, 'qrCard'])->name('barang-masuk.qr-card');
        Route::get('/barang-masuk/{id}/print', [BarangMasukController::class, 'print'])->name('barang-masuk.print');
        Route::get('/barang-masuk/{id}/cetak-pdf', [BarangMasukController::class, 'cetakPDF'])->name('barang-masuk.cetak.pdf');
        Route::get('/barang-masuk/{id}/cetak-ba', [BarangMasukController::class, 'cetakBeritaAcara'])->name('barang-masuk.cetak-berita-acara');
        Route::get('/barang-masuk/{id}/cetak-qr-kecil', [BarangMasukController::class, 'cetakQRKecil'])->name('barang-masuk.cetak-qr-kecil');

        Route::get('/barang-keluar/{id}/detail', [BarangKeluarController::class, 'show'])->name('barang-keluar.detail');
        Route::get('/barang-keluar/{id}/cetak-ba', [BarangKeluarController::class, 'cetakBA'])->name('barang-keluar.cetak-ba');
        Route::get('/barang-keluar/{id}/cetak-detail', [BarangKeluarController::class, 'cetakDetail'])->name('barang-keluar.cetak-detail');
    });

    // SUPERADMIN ONLY
    Route::middleware(['role:superadmin'])->group(function () {
        Route::get('/kelola-user', [UserController::class, 'index'])->name('user.index');
        Route::get('/kelola-user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/kelola-user', [UserController::class, 'store'])->name('user.store');
        Route::get('/kelola-user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/kelola-user/{user}', [UserController::class, 'update'])->name('user.update');
        Route::patch('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');
    });

    // Viewer/Admin (siapapun yang auth+verified) untuk lihat stok
    Route::get('/laporan-stok-viewer', [LaporanController::class, 'stokViewer'])->name('laporan.stok.viewer');
    Route::get('/laporan-stok-admin', [LaporanController::class, 'stokAdmin'])->name('laporan.stok.admin');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');

    
});

/*
|--------------------------------------------------------------------------
| Auth scaffolding (login, register, verifikasi email, dll.)
| (Semua route verifikasi email ada di sini → hindari duplikasi di web.php)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

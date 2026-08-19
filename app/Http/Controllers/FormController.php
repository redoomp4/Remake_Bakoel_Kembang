<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Pemasok;
use App\Models\Lokasi;
use App\Models\Kondisi;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class FormController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $data = [
            'kategories' => Kategori::where('user_id', $userId)->orderBy('kategori')->get(),
            'satuans'    => Satuan::where('user_id', $userId)->orderBy('nama_satuan')->get(),
            'pemasoks'   => Pemasok::where('user_id', $userId)->orderBy('nama_pemasok')->get(),
            'lokasis'    => Lokasi::where('user_id', $userId)->orderBy('nama_lokasi')->get(),
            'kondisis'   => Kondisi::where('user_id', $userId)->orderBy('nama_kondisi')->get(),
            'items'      => Item::where('user_id', $userId)->orderBy('nama_barang')->get(),
        ];

        return view('form.index', $data);
    }

    public function getOptions()
    {
        $userId = Auth::id();

        return response()->json([
            'success'    => true,
            'kategories' => Kategori::where('user_id', $userId)->orderBy('kategori')->select('id', 'kategori as nama')->get(),
            'satuans'    => Satuan::where('user_id', $userId)->orderBy('nama_satuan')->select('id', 'nama_satuan as nama')->get(),
            'pemasoks'   => Pemasok::where('user_id', $userId)->orderBy('nama_pemasok')->select('id', 'nama_pemasok as nama')->get(),
            'lokasis'    => Lokasi::where('user_id', $userId)->orderBy('nama_lokasi')->select('id', 'nama_lokasi as nama')->get(),
            'kondisis'   => Kondisi::where('user_id', $userId)->orderBy('nama_kondisi')->select('id', 'nama_kondisi as nama')->get(),
            'items'      => Item::where('user_id', $userId)->orderBy('nama_barang')->select('id', 'kode_barang', 'nama_barang', 'harga_dasar', 'stok')->get(),
        ]);
    }

    public function getItemDetail($kode)
    {
        $item = Item::where('user_id', Auth::id())
            ->where('kode_barang', $kode)
            ->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $item
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => [
                'required',
                'string',
                'max:150',
                Rule::unique('items', 'nama_barang')->where(fn($q) => $q->where('user_id', Auth::id())),
            ],
            'kategori_input' => ['required', 'string', 'max:100'],
            'satuan_input'   => ['required', 'string', 'max:100'],
            'stok_minimum'   => ['required', 'integer', 'min:0'],
            'harga_dasar'    => ['required', 'numeric', 'min:0'],
            'deskripsi'      => ['nullable', 'string', 'max:500'],
            'foto'           => ['nullable', 'image', 'max:2048'],
        ], [
            'nama_barang.required'    => 'Nama barang wajib diisi.',
            'nama_barang.unique'      => 'Nama barang sudah digunakan.',
            'kategori_input.required' => 'Kategori wajib diisi atau dipilih.',
            'satuan_input.required'   => 'Satuan wajib diisi atau dipilih.',
            'foto.image'              => 'File foto harus berupa gambar.',
            'foto.max'                => 'Ukuran foto maksimal 2MB.',
        ]);

        try {
            $userId = Auth::id();

            $namaKategori = trim((string) $request->kategori_input);
            $kategori = Kategori::firstOrCreate(
                ['user_id' => $userId, 'kategori' => $namaKategori],
                ['deskripsi' => 'Dibuat otomatis dari Form Item']
            );

            $namaSatuan = trim((string) $request->satuan_input);
            $satuan = Satuan::firstOrCreate(
                ['user_id' => $userId, 'nama_satuan' => $namaSatuan]
            );

            // 3. Simpan Item Utama
            $item = new Item();
            $item->user_id      = $userId;
            $item->kode_barang  = Item::generateKodeBarang();
            $item->public_token = (string) Str::uuid();
            $item->nama_barang  = trim((string) $request->nama_barang);
            $item->id_kategori  = $kategori->id;
            $item->id_satuan    = $satuan->id;
            $item->stok_minimum = (int) $request->stok_minimum;
            $item->harga_dasar  = (float) $request->harga_dasar;
            $item->deskripsi    = $request->deskripsi;

            if ($request->hasFile('foto')) {
                $item->foto = $request->file('foto')->store('foto_barang', 'public');
            }

            $item->save();

            // 4. Generate QR Code Item
            try {
                Storage::disk('public')->makeDirectory('qrcodes/items');

                $qrLink = route('item.public', [
                    'public_token' => $item->public_token
                ]);

                $filename = 'qr_item_' . $item->public_token . '.svg';
                $relativePath = 'qrcodes/items/' . $filename;

                $qr = QrCode::format('svg')
                    ->size(300)
                    ->margin(2)
                    ->generate($qrLink);

                Storage::disk('public')->put($relativePath, $qr);

                $item->update([
                    'qr_code' => $relativePath
                ]);
            } catch (\Throwable $qrException) {
                report($qrException);
            }

            return redirect()
                ->route('item.index')
                ->with('success', 'Item berhasil ditambahkan beserta QR Code.');
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            $mysqlErr = $e->errorInfo[1] ?? null;

            if ($sqlState === '23000' || $mysqlErr == 1062) {
                return back()
                    ->withInput()
                    ->with('error', 'Kode atau nama barang sudah digunakan.');
            }

            // DITAMPILKAN PESAN ERROR ASLI DARI DATABASE UNTUK DEBUGGING
            return back()
                ->withInput()
                ->with('error', 'DB Error: ' . $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            // DITAMPILKAN PESAN ERROR ASLI PHP UNTUK DEBUGGING
            return back()
                ->withInput()
                ->with('error', 'System Error: ' . $e->getMessage());
        }
    }

    public function parseVoice(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,wav,mp3,ogg,m4a|max:10240',
        ]);

        $groqKey = env('GROQ_API_KEY');

        if (empty($groqKey)) {
            return response()->json([
                'success' => false,
                'message' => 'GROQ_API_KEY belum dikonfigurasi di file .env.'
            ], 500);
        }

        try {
            $audioFile = $request->file('audio');

            // STEP 1: Transkripsi Audio ke Teks (Groq Whisper) ~ 0.3 Detik
            $sttResponse = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $groqKey,
                ])
                ->attach(
                    'file',
                    file_get_contents($audioFile->getRealPath()),
                    $audioFile->getClientOriginalName()
                )
                ->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                    'model' => 'whisper-large-v3-turbo',
                    'language' => 'id',
                    'response_format' => 'json',
                ]);

            if (!$sttResponse->successful()) {
                Log::error('Groq STT Error', ['body' => $sttResponse->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal menguraikan suara ke teks.'], 500);
            }

            $rawTranscript = $sttResponse->json('text');

            if (empty(trim($rawTranscript))) {
                return response()->json(['success' => false, 'message' => 'Suara tidak terdeteksi.'], 400);
            }

            $safeTranscript = substr(trim($rawTranscript), 0, 1000);

            // STEP 2: Koreksi Istilah Bunga & Format JSON (Groq Llama 3) ~ 0.5 Detik
            $prompt = <<<PROMPT
Ekstrak data Master Barang dari teks ucapan suara toko bunga ke JSON murni.
Input Suara: "{$safeTranscript}"

Skema JSON:
{
  "nama_barang": string|null,
  "kategori_input": string|null,
  "satuan_input": string|null,
  "stok_minimum": integer,
  "harga_dasar": number,
  "deskripsi": string|null
}

Aturan Ekstraksi:
1. NAMA BARANG (Manfaatkan Pengetahuan Botani & Fonetik AI):
   - Koreksi salah dengar/ejaan fonetik ucapan ke nama bunga/tanaman atau nama latin yang tepat (contoh fonetik: "katlea/katlya" -> "Anggrek Cattleya", "dendro" -> "Anggrek Dendrobium", "paleno/faleno" -> "Anggrek Phalaenopsis", "bebi bres" -> "Baby's Breath", "mewah" -> "Mawar").
   - Gunakan pengetahuan taksonomi/nama ilmiah bunga Anda untuk memperbaiki nama latin/lokal secara presisi.
   - Tambahkan prefiks "Anggrek" jika menyebutkan varietas anggrek (Cattleya, Vanda, Dendrobium, Oncidium, dll).
   - Format wajib Title Case / Capital Case (Contoh: "Anggrek Cattleya", "Bunga Lily").

2. HARGA & STOK:
   - "harga_dasar": Konversi sebutan nominal angka ke angka murni ("15rb" -> 15000, "seratus ribu" -> 100000). Default: 0.
   - "stok_minimum": Ekstrak angka stok minimal. Default: 0.

3. KATEGORI & SATUAN (Inferensi Otomatis jika tidak disebutkan):
   - Kategori: Pilih yang paling sesuai ("Anggrek Pot", "Bunga Potong", "Bunga Tangkai", "Buket", "Aksesoris Bunga").
   - Satuan: Pilih yang paling sesuai ("Pot", "Tangkai", "Ikat", "Pcs").

Kembalikan HANYA JSON valid tanpa teks/markdown tambahan.
PROMPT;

            $llamaResponse = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $groqKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'groq/compound',
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.1,
                ]);

            if (!$llamaResponse->successful()) {
                Log::error('Groq Llama Error', ['body' => $llamaResponse->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal memproses parsing teks.'], 500);
            }

            $rawText = $llamaResponse->json('choices.0.message.content');
            $parsedData = json_decode($rawText, true);

            return response()->json([
                'success' => true,
                'transcript_raw' => $rawTranscript,
                'data' => [
                    'nama_barang'    => isset($parsedData['nama_barang']) ? (string)$parsedData['nama_barang'] : null,
                    'kategori_input' => isset($parsedData['kategori_input']) ? (string)$parsedData['kategori_input'] : null,
                    'satuan_input'   => isset($parsedData['satuan_input']) ? (string)$parsedData['satuan_input'] : null,
                    'stok_minimum'   => isset($parsedData['stok_minimum']) ? (int)$parsedData['stok_minimum'] : 0,
                    'harga_dasar'    => isset($parsedData['harga_dasar']) ? (float)$parsedData['harga_dasar'] : 0,
                    'deskripsi'      => isset($parsedData['deskripsi']) ? (string)$parsedData['deskripsi'] : null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Voice Parsing Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Parsing voice khusus Tab Barang Masuk (Groq Whisper + Llama 3)
     */
    public function parseVoiceBarangMasuk(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,wav,mp3,ogg,m4a|max:10240',
        ]);

        $groqKey = env('GROQ_API_KEY');

        if (empty($groqKey)) {
            return response()->json([
                'success' => false,
                'message' => 'GROQ_API_KEY belum dikonfigurasi di file .env.'
            ], 500);
        }

        try {
            $audioFile = $request->file('audio');

            // STEP 1: Transkripsi Audio ke Teks via Whisper
            $sttResponse = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . $groqKey])
                ->attach('file', file_get_contents($audioFile->getRealPath()), $audioFile->getClientOriginalName())
                ->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                    'model'           => 'whisper-large-v3-turbo',
                    'language'        => 'id',
                    'response_format' => 'json',
                ]);

            if (!$sttResponse->successful()) {
                Log::error('Groq STT Error (Barang Masuk)', ['body' => $sttResponse->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal menguraikan suara ke teks.'], 500);
            }

            $rawTranscript = $sttResponse->json('text');

            if (empty(trim($rawTranscript))) {
                return response()->json(['success' => false, 'message' => 'Suara tidak terdeteksi.'], 400);
            }

            $safeTranscript = substr(trim($rawTranscript), 0, 1000);
            $today = now()->format('Y-m-d'); // Jangkar tanggal untuk kalkulasi waktu relatif AI

            // STEP 2: Ekstraksi Data Transaksi Barang Masuk via Llama 3
            $prompt = <<<PROMPT
Ekstrak data penerimaan barang dari teks suara ke JSON murni.
Acuan Hari Ini: {$today}
Input Suara: "{$safeTranscript}"

Skema JSON:
{
  "nama_barang": string|null,
  "jumlah": integer,
  "harga_beli": number,
  "tgl_masuk": "YYYY-MM-DD",
  "tgl_kadaluarsa": "YYYY-MM-DD"|null,
  "pemasok_input": string|null,
  "lokasi_input": string|null,
  "kondisi_input": string|null,
  "catatan": string|null
}

Aturan Ekstraksi:
1. tgl_masuk: Hitung tanggal relatif ("kemarin", "3 hari lalu", "10 juni") berbasis Acuan Hari Ini ({$today}). Default: "{$today}".
2. tgl_kadaluarsa: Ekstrak jika ada kata kunci exp/kadaluarsa/tahan hingga. Hitung berbasis Acuan Hari Ini. Default: null.
3. nama_barang: Rapikan ejaan bunga/barang (Contoh: "dendro" -> "Anggrek Dendrobium").
4. jumlah & harga_beli: Angka murni. Default jumlah = 1, harga_beli = 0 ("10rb" -> 10000).
5. lokasi_input & kondisi_input: Default lokasi "Gudang Utama", kondisi "Segar" jika tidak disebut.

Kembalikan HANYA JSON valid tanpa teks/markdown lain.
PROMPT;

            $llamaResponse = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $groqKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'           => 'groq/compound',
                    'response_format' => ['type' => 'json_object'],
                    'messages'        => [['role' => 'user', 'content' => $prompt]],
                    'temperature'     => 0.1,
                ]);

            if (!$llamaResponse->successful()) {
                Log::error('Groq Llama Error (Barang Masuk)', ['body' => $llamaResponse->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal memproses parsing teks.'], 500);
            }

            $rawText    = $llamaResponse->json('choices.0.message.content');
            $parsedData = json_decode($rawText, true);

            // STEP 3: Pencocokan ke Database Item milik User
            $userId       = Auth::id();
            $namaBarangAi = $parsedData['nama_barang'] ?? null;
            $matchedItem  = null;

            if (!empty($namaBarangAi)) {
                $searchLower = mb_strtolower(trim($namaBarangAi));

                $matchedItem = Item::where('user_id', $userId)
                    ->get()
                    ->first(function ($item) use ($searchLower) {
                        $itemNameLower = mb_strtolower($item->nama_barang);
                        return $itemNameLower === $searchLower
                            || str_contains($itemNameLower, $searchLower)
                            || str_contains($searchLower, $itemNameLower);
                    });
            }

            return response()->json([
                'success'        => true,
                'transcript_raw' => $rawTranscript,
                'data'           => [
                    'id_item'        => $matchedItem ? $matchedItem->id : null,
                    'nama_barang'    => $namaBarangAi,
                    'item_found'     => $matchedItem ? true : false,
                    'jumlah'         => isset($parsedData['jumlah']) ? (int)$parsedData['jumlah'] : 1,
                    'harga_beli'     => isset($parsedData['harga_beli']) ? (float)$parsedData['harga_beli'] : ($matchedItem ? $matchedItem->harga_dasar : 0),
                    'tgl_masuk'      => $parsedData['tgl_masuk'] ?? $today,
                    'tgl_kadaluarsa' => $parsedData['tgl_kadaluarsa'] ?? null,
                    'pemasok_input'  => isset($parsedData['pemasok_input']) ? (string)$parsedData['pemasok_input'] : null,
                    'lokasi_input'   => isset($parsedData['lokasi_input']) ? (string)$parsedData['lokasi_input'] : 'Gudang Utama',
                    'kondisi_input'  => isset($parsedData['kondisi_input']) ? (string)$parsedData['kondisi_input'] : 'Segar',
                    'catatan'        => isset($parsedData['catatan']) ? (string)$parsedData['catatan'] : null,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Voice Parsing Barang Masuk Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Menyimpan Transaksi Barang Masuk & Auto-Create Master Data jika belum ada
     */
    public function barangMasukStore(Request $request)
    {
        $request->validate([
            'id_item'        => ['required', 'exists:items,id'],
            'jumlah'         => ['required', 'integer', 'min:1'],
            'harga_beli'     => ['required', 'numeric', 'min:0'],
            'pemasok_input'  => ['required', 'string', 'max:150'],
            'lokasi_input'   => ['nullable', 'string', 'max:100'],
            'kondisi_input'  => ['nullable', 'string', 'max:100'],
            'tgl_masuk'      => ['required', 'date'],
            'tgl_kadaluarsa' => ['nullable', 'date', 'after_or_equal:tgl_masuk'],
            'catatan'        => ['nullable', 'string', 'max:500'],
        ], [
            'id_item.required'           => 'Barang wajib dipilih dari database master.',
            'id_item.exists'             => 'Barang yang dipilih tidak valid atau tidak terdaftar.',
            'jumlah.required'            => 'Jumlah barang wajib diisi.',
            'jumlah.min'                 => 'Jumlah barang minimal 1.',
            'harga_beli.required'        => 'Harga beli wajib diisi.',
            'pemasok_input.required'     => 'Nama pemasok wajib diisi.',
            'tgl_masuk.required'         => 'Tanggal masuk wajib diisi.',
            'tgl_kadaluarsa.after_or_equal' => 'Tanggal kadaluarsa tidak boleh sebelum tanggal masuk.',
        ]);

        try {
            return DB::transaction(function () use ($request) {
                $userId = Auth::id();

                // 1. PASTIKAN ITEM DITEMUKAN
                $item = Item::where('user_id', $userId)
                    ->where('id', $request->id_item)
                    ->firstOrFail();

                // 2. AUTO-CREATE PEMASOK (Jika Belum Ada)
                $pemasok = Pemasok::firstOrCreate(
                    ['user_id' => $userId, 'nama_pemasok' => trim((string) $request->pemasok_input)]
                );

                // 3. AUTO-CREATE LOKASI
                $lokasiName = !empty($request->lokasi_input) ? trim((string) $request->lokasi_input) : 'Gudang Utama';
                $lokasi     = Lokasi::firstOrCreate(
                    ['user_id' => $userId, 'nama_lokasi' => $lokasiName]
                );

                // 4. AUTO-CREATE KONDISI
                $kondisiName = !empty($request->kondisi_input) ? trim((string) $request->kondisi_input) : 'Segar';
                $kondisi     = Kondisi::firstOrCreate(
                    ['user_id' => $userId, 'nama_kondisi' => $kondisiName]
                );

                // 5. SIMPAN TRANSAKSI BARANG MASUK
                $barangMasuk                 = new BarangMasuk();
                $barangMasuk->user_id        = $userId;
                $barangMasuk->id_item        = $item->id;
                $barangMasuk->id_pemasok     = $pemasok->id;
                $barangMasuk->id_lokasi      = $lokasi->id;
                $barangMasuk->id_kondisi     = $kondisi->id;
                $barangMasuk->jumlah         = (int) $request->jumlah;
                $barangMasuk->harga_beli     = (float) $request->harga_beli;
                $barangMasuk->tgl_masuk      = $request->tgl_masuk;
                $barangMasuk->tgl_kadaluarsa = $request->tgl_kadaluarsa;
                $barangMasuk->catatan        = $request->catatan;
                $barangMasuk->save();

                // 6. UPDATE STOK DI MASTER ITEM
                $item->increment('stok', (int) $request->jumlah);

                return redirect()
                    ->route('barang-masuk.index')
                    ->with('success', 'Transaksi barang masuk berhasil dicatat!');
            });
        } catch (QueryException $e) {
            Log::error('DB Error Barang Masuk: ' . $e->getMessage());
            return back()->withInput()->with('error', 'DB Error: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('System Error Barang Masuk: ' . $e->getMessage());
            return back()->withInput()->with('error', 'System Error: ' . $e->getMessage());
        }
    }
    /**
     * Parsing voice khusus Tab Barang Keluar (Groq Whisper + Llama 3)
     */
    public function parseVoiceBarangKeluar(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:webm,wav,mp3,ogg,m4a|max:10240',
        ]);

        $groqKey = env('GROQ_API_KEY');

        if (empty($groqKey)) {
            return response()->json([
                'success' => false,
                'message' => 'GROQ_API_KEY belum dikonfigurasi di file .env.'
            ], 500);
        }

        try {
            $audioFile = $request->file('audio');

            // STEP 1: Transkripsi Audio ke Teks via Groq Whisper
            $sttResponse = Http::timeout(15)
                ->withHeaders(['Authorization' => 'Bearer ' . $groqKey])
                ->attach('file', file_get_contents($audioFile->getRealPath()), $audioFile->getClientOriginalName())
                ->post('https://api.groq.com/openai/v1/audio/transcriptions', [
                    'model'           => 'whisper-large-v3-turbo',
                    'language'        => 'id',
                    'response_format' => 'json',
                ]);

            if (!$sttResponse->successful()) {
                Log::error('Groq STT Error (Barang Keluar)', ['body' => $sttResponse->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal menguraikan suara ke teks.'], 500);
            }

            $rawTranscript = $sttResponse->json('text');

            if (empty(trim($rawTranscript))) {
                return response()->json(['success' => false, 'message' => 'Suara tidak terdeteksi.'], 400);
            }

            $safeTranscript = substr(trim($rawTranscript), 0, 1000);

            // STEP 2: Ekstraksi Data Transaksi Barang Keluar via Groq
            $prompt = <<<PROMPT
Ekstrak data pengeluaran barang/transaksi barang keluar dari teks suara ke JSON murni.
Input Suara: "{$safeTranscript}"

Skema JSON:
{
  "nama_barang": string|null,
  "jumlah_keluar": integer,
  "harga_jual": number,
  "penerima": string|null,
  "jenis_transaksi": string|null,
  "lokasi_tujuan": string|null,
  "catatan": string|null
}

Aturan Ekstraksi:
1. NAMA BARANG: Rapikan ejaan bunga/barang sesuai nama master (Contoh: "katlea" -> "Anggrek Cattleya", "dendro" -> "Anggrek Dendrobium").
2. JUMLAH & HARGA JUAL:
   - "jumlah_keluar": Angka murni jumlah unit pengeluaran. Default = 1.
   - "harga_jual": Konversi nominal ke angka murni ("15rb" -> 15000, "seratus ribu" -> 100000). Default = 0.
3. JENIS TRANSAKSI: Pilih satu dari opsi berikut jika disebutkan: ["Penjualan", "Donasi", "Pemakaian Internal", "Pemindahan Barang", "Retur ke Supplier", "Penghapusan", "Lainnya"]. Default: "Penjualan".
4. PENERIMA & LOKASI TUJUAN: Ekstrak nama penerima barang dan lokasi/toko tujuan jika diucapkan.

Kembalikan HANYA JSON valid tanpa teks/markdown tambahan.
PROMPT;

            $llamaResponse = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $groqKey,
                    'Content-Type'  => 'application/json',
                ])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model'           => 'groq/compound',
                    'response_format' => ['type' => 'json_object'],
                    'messages'        => [['role' => 'user', 'content' => $prompt]],
                    'temperature'     => 0.1,
                ]);

            if (!$llamaResponse->successful()) {
                Log::error('Groq Llama Error (Barang Keluar)', ['body' => $llamaResponse->body()]);
                return response()->json(['success' => false, 'message' => 'Gagal memproses parsing teks.'], 500);
            }

            $rawText    = $llamaResponse->json('choices.0.message.content');
            $parsedData = json_decode($rawText, true);

            // STEP 3: Pencocokan ke Database Item milik User
            $userId       = Auth::id();
            $namaBarangAi = $parsedData['nama_barang'] ?? null;
            $matchedItem  = null;

            if (!empty($namaBarangAi)) {
                $searchLower = mb_strtolower(trim($namaBarangAi));

                $matchedItem = Item::where('user_id', $userId)
                    ->get()
                    ->first(function ($item) use ($searchLower) {
                        $itemNameLower = mb_strtolower($item->nama_barang);
                        return $itemNameLower === $searchLower
                            || str_contains($itemNameLower, $searchLower)
                            || str_contains($searchLower, $itemNameLower);
                    });
            }

            return response()->json([
                'success'        => true,
                'transcript_raw' => $rawTranscript,
                'data'           => [
                    'item_id'         => $matchedItem ? $matchedItem->id : null,
                    'nama_barang'     => $namaBarangAi,
                    'item_found'      => (bool) $matchedItem,
                    'jumlah_keluar'   => isset($parsedData['jumlah_keluar']) ? max(1, (int)$parsedData['jumlah_keluar']) : 1,
                    'harga_jual'      => isset($parsedData['harga_jual']) && $parsedData['harga_jual'] > 0
                        ? (float)$parsedData['harga_jual']
                        : ($matchedItem ? (float)$matchedItem->harga_dasar : 0),
                    'penerima'        => $parsedData['penerima'] ?? null,
                    'jenis_transaksi' => $parsedData['jenis_transaksi'] ?? 'Penjualan',
                    'lokasi_tujuan'   => $parsedData['lokasi_tujuan'] ?? null,
                    'catatan'         => $parsedData['catatan'] ?? null,
                    'stok_tersedia'   => $matchedItem ? $matchedItem->stok : 0,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Voice Parsing Barang Keluar Exception: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil daftar pilihan barang unik yang cocok dengan skrip JS Blade
     */
    public function getPilihanBarang()
    {
        $userId = Auth::id();

        $barangMasuks = BarangMasuk::with(['item.satuan', 'lokasi', 'kondisi'])
            ->where('user_id', $userId)
            ->get();

        // Query semua barang keluar sekaligus untuk mencegah N+1 Query Problem
        $barangKeluars = BarangKeluar::where('user_id', $userId)
            ->get()
            ->groupBy(fn($row) => $row->item_id . '|' . $row->id_lokasi . '|' . $row->id_kondisi);

        $grouped = $barangMasuks
            ->groupBy(fn($barang) => $barang->item_id . '|' . $barang->id_lokasi . '|' . $barang->id_kondisi)
            ->map(function ($group, $key) use ($barangKeluars) {

                $latest = $group->sortByDesc('tanggal_masuk')->sortByDesc('id')->first();

                if (!$latest || !$latest->item) {
                    return null;
                }

                $masuk  = $group->sum('jumlah');
                $keluar = isset($barangKeluars[$key]) ? $barangKeluars[$key]->sum('jumlah_keluar') : 0;
                $stok   = $masuk - $keluar;

                $totalHarga  = $group->sum(fn($row) => $row->harga_satuan * $row->jumlah);
                $totalJumlah = $group->sum('jumlah');
                $wac         = $totalJumlah > 0 ? ($totalHarga / $totalJumlah) : 0;

                return [
                    'item_id'     => $latest->item_id,
                    'kode'        => $latest->item->kode_barang ?? $latest->item->kode ?? ('BRG-' . $latest->item_id),
                    'nama_barang' => $latest->item->nama_barang ?? '-',
                    'lokasi_id'   => $latest->id_lokasi,
                    'lokasi'      => $latest->lokasi?->nama_lokasi ?? '-',
                    'kondisi_id'  => $latest->id_kondisi,
                    'kondisi'     => $latest->kondisi?->nama_kondisi ?? '-',
                    'stok'        => max(0, $stok),
                    'satuan'      => $latest->item->satuan?->nama_satuan ?? '-',
                    'harga_dasar' => (int) round($wac),
                ];
            })
            ->filter()
            ->values();

        // Fallback jika belum ada transaksi Barang Masuk
        if ($grouped->isEmpty()) {
            $defaultLokasi  = Lokasi::where('user_id', $userId)->first();
            $defaultKondisi = Kondisi::where('user_id', $userId)->first();
            $masterItems    = Item::with('satuan')->where('user_id', $userId)->get();

            $grouped = $masterItems->map(function ($item) use ($defaultLokasi, $defaultKondisi) {
                return [
                    'item_id'     => $item->id,
                    'kode'        => $item->kode_barang ?? $item->kode ?? ('BRG-' . $item->id),
                    'nama_barang' => $item->nama_barang,
                    'lokasi_id'   => $defaultLokasi?->id ?? 1,
                    'lokasi'      => $defaultLokasi?->nama_lokasi ?? 'Gudang Utama',
                    'kondisi_id'  => $defaultKondisi?->id ?? 1,
                    'kondisi'     => $defaultKondisi?->nama_kondisi ?? 'Baik',
                    'stok'        => (int) $item->stok,
                    'satuan'      => $item->satuan?->nama_satuan ?? 'Pcs',
                    'harga_dasar' => (int) round($item->harga_dasar ?? 0),
                ];
            });
        }

        return response()->json($grouped);
    }
    /**
     * API DETAIL BARANG (Diperbaiki Validasi Parameter)
     */
    public function getDetailBarang(Request $request)
    {
        $userId    = Auth::id();
        $itemId    = $request->input('item_id');
        $idLokasi  = $request->input('id_lokasi');
        $idKondisi = $request->input('id_kondisi');

        if (!$itemId) {
            return response()->json([
                'stok'        => 0,
                'nama_barang' => '-',
                'satuan'      => '-',
                'lokasi'      => '-',
                'kondisi'     => '-',
                'harga_dasar' => 0,
            ]);
        }

        $item = Item::with('satuan')
            ->where('id', $itemId)
            ->where('user_id', $userId)
            ->first();

        if (!$item) {
            return response()->json([
                'stok'        => 0,
                'nama_barang' => '-',
                'satuan'      => '-',
                'lokasi'      => '-',
                'kondisi'     => '-',
                'harga_dasar' => 0,
            ]);
        }

        $lokasi  = Lokasi::where('id', $idLokasi)->where('user_id', $userId)->first();
        $kondisi = Kondisi::where('id', $idKondisi)->where('user_id', $userId)->first();

        $masuk = BarangMasuk::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->when($idLokasi, fn($q) => $q->where('id_lokasi', $idLokasi))
            ->when($idKondisi, fn($q) => $q->where('id_kondisi', $idKondisi))
            ->sum('jumlah');

        $keluar = BarangKeluar::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->when($idLokasi, fn($q) => $q->where('id_lokasi', $idLokasi))
            ->when($idKondisi, fn($q) => $q->where('id_kondisi', $idKondisi))
            ->sum('jumlah_keluar');

        // WAC via Query SQL
        $wac = (float) BarangMasuk::where('user_id', $userId)
            ->where('item_id', $item->id)
            ->when($idLokasi, fn($q) => $q->where('id_lokasi', $idLokasi))
            ->when($idKondisi, fn($q) => $q->where('id_kondisi', $idKondisi))
            ->selectRaw('COALESCE(SUM(harga_satuan * jumlah) / NULLIF(SUM(jumlah), 0), 0) as wac')
            ->value('wac');

        return response()->json([
            'stok'        => max(0, $masuk - $keluar),
            'nama_barang' => $item->nama_barang ?? '-',
            'satuan'      => $item->satuan?->nama_satuan ?? '-',
            'lokasi'      => $lokasi?->nama_lokasi ?? '-',
            'kondisi'     => $kondisi?->nama_kondisi ?? '-',
            'id_lokasi'   => $idLokasi,
            'id_kondisi'  => $idKondisi,
            'harga_dasar' => (int) round($wac > 0 ? $wac : $item->harga_dasar),
        ]);
    }
    /**
     * Menyimpan Transaksi Barang Keluar & Mengurangi Stok Master Item
     */
    public function barangKeluarStore(Request $request)
    {
        // 1. Parsing gabungan value 'kode_lokasi_kondisi' (KODE|LOKASI_ID|KONDISI_ID)
        if ($request->filled('kode_lokasi_kondisi') && (!$request->filled('id_lokasi') || !$request->filled('id_kondisi'))) {
            $parts = explode('|', $request->kode_lokasi_kondisi);
            if (count($parts) === 3) {
                $request->merge([
                    'id_lokasi'  => $parts[1],
                    'id_kondisi' => $parts[2],
                ]);
            }
        }

        // 2. Validasi Form
        $request->validate([
            'item_id'         => ['required', 'exists:items,id'],
            'id_lokasi'       => ['required', 'exists:lokasis,id'],
            'id_kondisi'      => ['required', 'exists:kondisis,id'],
            'jumlah_keluar'   => ['required', 'integer', 'min:1'],
            'harga_jual'      => ['required', 'numeric', 'min:0'],
            'penerima'        => ['required', 'string', 'max:150'],
            'jenis_transaksi' => ['required', 'string', 'max:100'],
            'lokasi_tujuan'   => ['required', 'string', 'max:150'],
            'catatan'         => ['nullable', 'string', 'max:500'],
        ], [
            'item_id.required'         => 'Pilihan barang wajib diisi.',
            'item_id.exists'           => 'Barang tidak ditemukan di database.',
            'id_lokasi.required'       => 'Lokasi asal wajib diisi.',
            'id_kondisi.required'      => 'Kondisi barang wajib diisi.',
            'jumlah_keluar.required'   => 'Jumlah barang keluar wajib diisi.',
            'jumlah_keluar.min'        => 'Jumlah barang keluar minimal 1.',
            'harga_jual.required'      => 'Harga jual wajib diisi.',
            'penerima.required'        => 'Nama penerima wajib diisi.',
            'jenis_transaksi.required' => 'Jenis transaksi wajib dipilih.',
            'lokasi_tujuan.required'   => 'Lokasi tujuan wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $userId = Auth::id();

                // 3. Ambil Item & Validasi Ketersediaan Stok
                $item = Item::where('user_id', $userId)
                    ->where('id', $request->item_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Melempar Exception agar DB::transaction membatalkan (rollback) transaksi jika stok kurang
                if ($item->stok < $request->jumlah_keluar) {
                    throw new \Exception("Stok tidak mencukupi! Stok saat ini: {$item->stok}, permintaan keluar: {$request->jumlah_keluar}.");
                }

                // 4. Hitung Total Nilai Jual
                $totalHargaJual = (float)$request->jumlah_keluar * (float)$request->harga_jual;

                // 5. Simpan Record Barang Keluar
                $barangKeluar                   = new BarangKeluar();
                $barangKeluar->user_id          = $userId;
                $barangKeluar->id_item          = $item->id;
                $barangKeluar->id_lokasi        = $request->id_lokasi;
                $barangKeluar->id_kondisi       = $request->id_kondisi;
                $barangKeluar->jumlah_keluar    = (int) $request->jumlah_keluar;
                $barangKeluar->harga_jual       = (float) $request->harga_jual;
                $barangKeluar->total_harga_jual = $totalHargaJual;
                $barangKeluar->penerima         = trim((string) $request->penerima);
                $barangKeluar->jenis_transaksi  = $request->jenis_transaksi;
                $barangKeluar->lokasi_tujuan    = trim((string) $request->lokasi_tujuan);
                $barangKeluar->catatan          = $request->catatan;
                $barangKeluar->save();

                // 6. Kurangi Stok pada Master Item
                $item->decrement('stok', (int) $request->jumlah_keluar);
            });

            return redirect()
                ->route('barang-keluar.index')
                ->with('success', 'Transaksi barang keluar berhasil dicatat!');
        } catch (\Exception $e) {
            Log::error('Error Barang Keluar: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}

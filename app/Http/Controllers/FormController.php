<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use App\Models\Pemasok;
use App\Models\Lokasi;
use App\Models\Kondisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
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
Anda adalah sistem ekstraksi data toko bunga profesional.
Tugas Anda: Analisis teks input dari ucapan suara, perbaiki ejaan nama bunga/anggrek, lalu keluarkan JSON sesuai format.

Teks Input Suara:
"{$safeTranscript}"

Skema JSON Wajib:
{
  "nama_barang": string|null,
  "kategori_input": string|null,
  "satuan_input": string|null,
  "stok_minimum": integer,
  "harga_dasar": number,
  "deskripsi": string|null
}

Aturan Penafsiran Nama Bunga & Anggrek:
1. PERBAIKI NAMA BUNGA:
   - "katlya" / "katlea" / "katlya mant" -> "Anggrek Cattleya"
   - "dendro" / "dendrobium" -> "Anggrek Dendrobium"
   - "bulan" / "anggrek bulan" -> "Anggrek Bulan"
   - "vanda" -> "Anggrek Vanda"
   - "bebi bres" / "baby breath" -> "Baby's Breath"
   - "mewah" / "mawar" -> "Mawar"
   - "krisan" / "seruni" -> "Krisan"
   - Format nama_barang harus Capital Case.
2. HARGA & STOK:
   - "harga_dasar": Konversi sebutan uang ke angka murni (Contoh: "15rb" -> 15000, "seratus ribu" -> 100000). Jika tidak ada, isi 0.
   - "stok_minimum": Angka stok awal (default 0).
3. KATEGORI & SATUAN (Jika tidak ada di ucapan, tebak yang paling relevan):
   - Kategori: "Bunga Tangkai", "Bunga Potong", "Anggrek Pot", "Buket", "Aksesoris Bunga".
   - Satuan: "Tangkai", "Pot", "Ikat", "Pcs".

Kembalikan HANYA JSON murni tanpa teks/markdown tambahan.
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
}

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
        $request->validate(['text' => 'required|string']);

        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'API Key tidak ditemukan'], 500);
        }

        $userSpeech = $request->input('text');
        $prompt = "..."; // sama seperti sebelumnya

        try {
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key={$apiKey}", [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature' => 0.2,
                    'maxOutputTokens' => 200,
                ]
            ]);

            if (!$response->successful()) {
                \Log::error('Gemini error: ' . $response->body());
                return response()->json(['success' => false, 'message' => 'Gagal terhubung ke AI'], 500);
            }

            $rawText = $response->json('candidates.0.content.parts.0.text');
            // Bersihkan kemungkinan markdown
            $cleanJson = preg_replace('/```json|```/', '', $rawText);
            $parsedData = json_decode($cleanJson, true);

            if (!is_array($parsedData)) {
                \Log::error('Gagal parse JSON dari Gemini: ' . $rawText);
                return response()->json(['success' => false, 'message' => 'Respons AI tidak valid'], 500);
            }

            return response()->json([
                'success' => true,
                'data' => $parsedData
            ]);
        } catch (\Exception $e) {
            \Log::error('Voice parse error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

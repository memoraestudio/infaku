<?php

namespace App\Http\Controllers\Admin\Kelompok;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class KeluargaController extends Controller
{
    // ============================================
    // SECTION 1: VIEW METHODS
    // ============================================

    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        $infoKelompok = DB::table('master_kelompok')
            ->where('kelompok_id', $kelompokId)
            ->first();

        return view('admin.ku.kelompok.data_keluarga', [
            'user' => $user,
            'info_kelompok' => $infoKelompok
        ]);
    }

    public function print(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        $keluargas = DB::table('keluarga')
            ->join('jamaah', 'keluarga.kepala_keluarga_id', '=', 'jamaah.jamaah_id')
            ->where('keluarga.kelompok_id', $kelompokId)
            ->select('keluarga.*', 'jamaah.nama_lengkap as kepala_keluarga_nama')
            ->orderBy('keluarga.nama_keluarga')
            ->get();

        $infoKelompok = DB::table('master_kelompok')
            ->where('kelompok_id', $kelompokId)
            ->first();

        return view('admin.ku.kelompok.print_data_keluarga', [
            'keluargas' => $keluargas,
            'info_kelompok' => $infoKelompok,
            'user' => $user
        ]);
    }

    // ============================================
    // SECTION 2: API METHODS
    // ============================================

    public function getData(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Pastikan page dan per_page integer
            $page = (int) $page;
            $perPage = (int) $perPage;
            if ($page < 1) $page = 1;
            if ($perPage < 1) $perPage = 10;

            // Hitung offset
            $offset = ($page - 1) * $perPage;

            // Build query
            $query = DB::table('keluarga')
                ->join('jamaah', 'keluarga.kepala_keluarga_id', '=', 'jamaah.jamaah_id')
                ->where('keluarga.kelompok_id', $kelompokId);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('keluarga.no_kk', 'like', "%{$search}%")
                        ->orWhere('keluarga.nama_keluarga', 'like', "%{$search}%")
                        ->orWhere('jamaah.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('keluarga.alamat', 'like', "%{$search}%");
                });
            }

            // Get total count
            $total = $query->count();

            // Hitung total pages
            $totalPages = ceil($total / $perPage);
            if ($totalPages < 1) $totalPages = 1;

            // Jika page > totalPages, reset ke page = 1
            if ($page > $totalPages) {
                $page = 1;
                $offset = 0;
            }

            // Get paginated data
            $data = $query->select('keluarga.*', 'jamaah.nama_lengkap as kepala_keluarga_nama')
                ->orderBy('keluarga.created_at', 'desc')
                ->offset($offset)
                ->limit($perPage)
                ->get();

            // Debug log
            Log::info('Keluarga Pagination Debug', [
                'page' => $page,
                'per_page' => $perPage,
                'offset' => $offset,
                'total' => $total,
                'total_pages' => $totalPages,
                'data_count' => $data->count()
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
                'current_page' => $page,
                'last_page' => $totalPages,
                'total' => $total,
                'per_page' => $perPage,
                'from' => $offset + 1,
                'to' => min($offset + $perPage, $total)
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting keluarga data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data keluarga'
            ], 500);
        }
    }

    public function getJamaahOptions(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];
            $search = $request->get('search', '');

            $query = DB::table('jamaah')
                ->leftJoin('keluarga', 'jamaah.jamaah_id', '=', 'keluarga.kepala_keluarga_id')
                ->where('jamaah.kelompok_id', $kelompokId)
                ->where('jamaah.jenis_kelamin', 'L')
                ->where('jamaah.is_aktif', true)
                ->whereNull('keluarga.kepala_keluarga_id');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('jamaah.nama_lengkap', 'like', "%{$search}%");
                });
            }

            $jamaahs = $query->select('jamaah.jamaah_id', 'jamaah.nama_lengkap')
                ->orderBy('jamaah.nama_lengkap')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $jamaahs
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting jamaah options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jamaah'
            ], 500);
        }
    }

    public function getJamaahFam(Request $request)
    {
        dd($request->all());
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];
            $search = $request->get('search', '');

            $query = DB::table('jamaah')
                ->leftJoin('keluarga', 'jamaah.jamaah_id', '=', 'keluarga.kepala_keluarga_id')
                ->leftJoin('anggota_keluarga', 'jamaah.jamaah_id', '=', 'anggota_keluarga.jamaah_id')
                ->where('jamaah.kelompok_id', $kelompokId)
                ->where('jamaah.is_aktif', true)
                ->whereNull('keluarga.kepala_keluarga_id')
                ->whereNull('anggota_keluarga.jamaah_id');

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('jamaah.nama_lengkap', 'like', "%{$search}%");
                });
            }

            $jamaahs = $query->select('jamaah.jamaah_id', 'jamaah.nama_lengkap')
                ->orderBy('jamaah.nama_lengkap')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $jamaahs
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting jamaah options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jamaah'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $keluarga = DB::table('keluarga')
                ->join('jamaah', 'keluarga.kepala_keluarga_id', '=', 'jamaah.jamaah_id')
                ->where('keluarga.keluarga_id', $id)
                ->select('keluarga.*', 'jamaah.nama_lengkap as kepala_keluarga_nama', 'jamaah.telepon as telepon')
                ->first();

            if (!$keluarga) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data keluarga tidak ditemukan'
                ], 404);
            }

            // Get anggota keluarga
            $anggota = DB::table('anggota_keluarga')
                ->join('jamaah', 'anggota_keluarga.jamaah_id', '=', 'jamaah.jamaah_id')
                ->where('anggota_keluarga.keluarga_id', $id)
                ->where('anggota_keluarga.jamaah_id', '!=', $keluarga->kepala_keluarga_id)
                ->select('jamaah.nama_lengkap', 'anggota_keluarga.status_hubungan')
                ->get();

            $keluarga->anggota = $anggota;

            return response()->json([
                'success' => true,
                'data' => $keluarga
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data keluarga'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama_keluarga' => 'required|string|max:100',
                'kepala_keluarga_id' => 'required|exists:jamaah,jamaah_id',
                'alamat' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            // Generate keluarga_id (format: KF001, KF002, dst)
            $lastKeluarga = DB::table('keluarga')
                ->where('kelompok_id', $kelompokId)
                ->orderByRaw('CAST(SUBSTRING(keluarga_id, 3) AS UNSIGNED) DESC')
                ->first();

            if ($lastKeluarga && !empty($lastKeluarga->keluarga_id)) {
                $lastId = $lastKeluarga->keluarga_id;
                $lastNumber = (int) substr($lastId, 2); // Ambil setelah 2 karakter "KF"
                $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $newNumber = '001';
            }

            $keluargaId = 'KF' . $newNumber;

            // Mulai transaction
            DB::beginTransaction();

            try {
                // Insert ke tabel keluarga
                $keluargaData = [
                    'keluarga_id' => $keluargaId,
                    'nama_keluarga' => $request->input('nama_keluarga'),
                    'kepala_keluarga_id' => $request->input('kepala_keluarga_id'),
                    'alamat' => $request->input('alamat'),
                    'kelompok_id' => $kelompokId,
                    'created_at' => DB::raw('NOW()')
                ];

                DB::table('keluarga')->insert($keluargaData);

                // Add kepala keluarga to anggota_keluarga
                DB::table('anggota_keluarga')->insert([
                    'keluarga_id' => $keluargaId,
                    'jamaah_id' => $request->input('kepala_keluarga_id'),
                    'status_hubungan' => 'KEPALA KELUARGA',
                    'urutan' => 1,
                    'created_at' => DB::raw('NOW()')
                ]);

                // Update status menikah jamaah
                DB::table('jamaah')
                    ->where('jamaah_id', $request->input('kepala_keluarga_id'))
                    ->update(['status_menikah' => 'MENIKAH', 'updated_at' => DB::raw('NOW()')]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data keluarga berhasil ditambahkan',
                    'data' => [
                        'keluarga_id' => $keluargaId
                    ]
                ]);
            } catch (\Exception $e) {
                // Rollback jika ada error
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error storing keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    public function insertAnggotaKeluarga(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'keluarga_id' => 'required|string',
                'jamaah_id' => 'required|string',
                'status_hubungan' => 'required|string',
                'urutan' => 'nullable|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mulai transaction
            DB::beginTransaction();

            try {
                // Insert anggota keluarga
                DB::table('anggota_keluarga')->insert([
                    'keluarga_id' => $request->keluarga_id,
                    'jamaah_id' => $request->jamaah_id,
                    'status_hubungan' => strtoupper($request->status_hubungan),
                    'urutan' => $request->urutan,
                    'created_at' => DB::raw('NOW()'),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Anggota keluarga berhasil ditambahkan'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error inserting anggota keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah anggota keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama_keluarga' => 'required|string|max:100',
                'kepala_keluarga_id' => 'required|exists:jamaah,jamaah_id',
                'alamat' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Mulai transaction
            DB::beginTransaction();

            try {
                // Update data keluarga
                $updateData = [
                    'nama_keluarga' => $request->input('nama_keluarga'),
                    'kepala_keluarga_id' => $request->input('kepala_keluarga_id'),
                    'alamat' => $request->input('alamat'),
                    'updated_at' => DB::raw('NOW()')
                ];

                DB::table('keluarga')
                    ->where('keluarga_id', $id)
                    ->update($updateData);

                // Update kepala keluarga in anggota_keluarga
                DB::table('anggota_keluarga')
                    ->where('keluarga_id', $id)
                    ->where('status_hubungan', 'KEPALA KELUARGA')
                    ->update([
                        'jamaah_id' => $request->input('kepala_keluarga_id'),
                        'updated_at' => DB::raw('NOW()')
                    ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data keluarga berhasil diupdate'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            Log::error('Error updating keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            // Delete anggota keluarga first
            DB::table('anggota_keluarga')
                ->where('keluarga_id', $id)
                ->delete();

            // Delete keluarga
            DB::table('keluarga')
                ->where('keluarga_id', $id)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }
}

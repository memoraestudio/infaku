<?php

namespace App\Http\Controllers\Admin\Kelompok;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KeluargaController extends Controller
{
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
            ->leftJoin('anggota_keluarga', 'keluarga.keluarga_id', '=', 'anggota_keluarga.keluarga_id')
            ->where('keluarga.kelompok_id', $kelompokId)
            ->select(
                'keluarga.*',
                'jamaah.nama_lengkap as kepala_keluarga_nama',
                DB::raw('COUNT(anggota_keluarga.keluarga_id) as total_anggota')
            )
            ->groupBy('keluarga.keluarga_id', 'jamaah.nama_lengkap') // penting
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

    public function getData(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

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

            $total = $query->count();
            $data = $query->select('keluarga.*', 'jamaah.nama_lengkap as kepala_keluarga_nama')
                ->orderBy('keluarga.created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'current_page' => (int)$page,
                'last_page' => ceil($total / $perPage),
                'total' => $total
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
            $user = $request->session()->get('user');
            $kelompokId = DB::table('master_kelompok')
                ->where('kelompok_id', $user['wilayah_id'])
                ->value('nama_kelompok');

            $validated = $request->validate([
                'nama_keluarga' => 'required|string|max:100',
                'kepala_keluarga_id' => 'required|exists:jamaah,jamaah_id',
                'alamat' => 'nullable|string',
            ]);

            // Generate keluarga_id
            $lastKeluarga = DB::table('keluarga')
                ->where('kelompok_id', $kelompokId)
                ->orderBy('created_at', 'desc')
                ->first();

            $nextNumber = $lastKeluarga ? (int)substr($lastKeluarga->keluarga_id, 2) + 1 : 1;
            $keluargaId = 'KF' . $kelompokId . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $validated['keluarga_id'] = $keluargaId;
            $validated['kelompok_id'] = $kelompokId;
            $validated['created_at'] = now();

            DB::table('keluarga')->insert($validated);

            // Add kepala keluarga to anggota_keluarga
            DB::table('anggota_keluarga')->insert([
                'keluarga_id' => $keluargaId,
                'jamaah_id' => $validated['kepala_keluarga_id'],
                'status_hubungan' => 'KEPALA KELUARGA',
                'urutan' => 1,
                'created_at' => now()
            ]);

            DB::table('jamaah')
                ->where('jamaah_id', $validated['kepala_keluarga_id'])
                ->update(['status_menikah' => 'MENIKAH', 'updated_at' => now()]);

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Insert anggota keluarga (from modal)
     */
    public function insertAnggotaKeluarga(Request $request)
    {
        $request->validate([
            'keluarga_id' => 'required|string',
            'jamaah_id' => 'required|string',
            'status_hubungan' => 'required|string',
            'urutan' => 'nullable|integer',
        ]);

        try {
            $user = $request->session()->get('user');
            $kelompokId = DB::table('master_kelompok')
                ->select('nama_kelompok')
                ->where('kelompok_id', $user['wilayah_id'])
                ->value('nama_kelompok');
            dd($kelompokId);

            $anggotaId = DB::table('anggota_keluarga')->max('anggota_id');
            $anggotaId = $anggotaId ? $anggotaId + 1 : 1;

            DB::table('anggota_keluarga')->insert([
                // 'anggota_id' => $anggotaId,
                'keluarga_id' => $request->keluarga_id,
                'jamaah_id' => $request->jamaah_id,
                'status_hubungan' => strtoupper($request->status_hubungan),
                'urutan' => $request->urutan,
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Anggota keluarga berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error inserting anggota keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah anggota keluarga'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_keluarga' => 'required|string|max:100',
                'kepala_keluarga_id' => 'required|exists:jamaah,jamaah_id',
                'telepon' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
            ]);

            $validated['updated_at'] = now();

            DB::table('keluarga')
                ->where('keluarga_id', $id)
                ->update($validated);

            // Update kepala keluarga in anggota_keluarga
            DB::table('anggota_keluarga')
                ->where('keluarga_id', $id)
                ->where('status_hubungan', 'KEPALA_KELUARGA')
                ->update([
                    'jamaah_id' => $validated['kepala_keluarga_id'],
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Data keluarga berhasil diupdate'
            ]);
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

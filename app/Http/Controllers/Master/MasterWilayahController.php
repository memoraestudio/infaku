<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterWilayahController extends Controller
{
    /**
     * Display master wilayah page for kelompok
     */
    public function wilayahKelompok()
    {
        return view('admin.master.kelompok');
    }

    /**
     * Get paginated wilayah data for kelompok
     */
    public function getWilayahKelompok(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];
            // dd($kelompokId);

            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $query = DB::table('master_kelompok')
                ->where('kelompok_id', $kelompokId); // Filter by desa

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_kelompok', 'LIKE', "%{$search}%")
                        ->orWhere('nama_masjid', 'LIKE', "%{$search}%")
                        ->orWhere('alamat_masjid', 'LIKE', "%{$search}%");
                });
            }

            $total = $query->count();
            $data = $query->orderBy('nama_kelompok')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $data,
                'current_page' => (int)$page,
                'last_page' => ceil($total / $perPage),
                'total' => $total,
                'per_page' => $perPage
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting wilayah kelompok: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data wilayah'
            ], 500);
        }
    }

    /**
     * Store new kelompok
     */
    public function storeWilayahKelompok(Request $request)
    {
        // dd($request->all());
        try {
            $user = $request->session()->get('user');
            $desaId = substr($user['wilayah_id'], 0, 9);
            // dd($desaId);

            $request->validate([
                'nama_kelompok' => 'required|string|max:60',
                'nama_masjid' => 'nullable|string|max:100',
                'alamat_masjid' => 'nullable|string',
                'keterangan' => 'nullable|string'
            ]);

            // Generate kelompok ID
            $lastKelompok = DB::table('master_kelompok')
                ->where('desa_id', $desaId)
                ->orderBy('kelompok_id', 'desc')
                ->first();
            dd($lastKelompok);

            $nextNumber = $lastKelompok ? (int)substr($lastKelompok->kelompok_id, -2) + 1 : 1;
            $kelompokId = $desaId . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);


            DB::table('master_kelompok')->insert([
                'kelompok_id' => $kelompokId,
                'desa_id' => $desaId,
                'nama_kelompok' => $request->nama_kelompok,
                'nama_masjid' => $request->nama_masjid,
                'alamat_masjid' => $request->alamat_masjid,
                'keterangan' => $request->keterangan,
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Kelompok berhasil ditambahkan',
                'data' => ['kelompok_id' => $kelompokId]
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing wilayah kelompok: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan kelompok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific kelompok
     */
    public function showWilayahKelompok($id)
    {
        dd(Auth::user());
        try {
            $kelompok = DB::table('master_kelompok')->where('kelompok_id', $id)->first();

            if (!$kelompok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelompok tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $kelompok
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing wilayah kelompok: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kelompok'
            ], 500);
        }
    }

    /**
     * Update kelompok
     */
    public function updateWilayahKelompok(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_kelompok' => 'required|string|max:60',
                'nama_masjid' => 'nullable|string|max:100',
                'alamat_masjid' => 'nullable|string',
                'keterangan' => 'nullable|string'
            ]);

            $kelompok = DB::table('master_kelompok')->where('kelompok_id', $id)->first();

            if (!$kelompok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelompok tidak ditemukan'
                ], 404);
            }

            DB::table('master_kelompok')
                ->where('kelompok_id', $id)
                ->update([
                    'nama_kelompok' => $request->nama_kelompok,
                    'nama_masjid' => $request->nama_masjid,
                    'alamat_masjid' => $request->alamat_masjid,
                    'keterangan' => $request->keterangan,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Kelompok berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating wilayah kelompok: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate kelompok: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete kelompok
     */
    public function destroyWilayahKelompok($id)
    {
        try {
            $kelompok = DB::table('master_kelompok')->where('kelompok_id', $id)->first();

            if (!$kelompok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kelompok tidak ditemukan'
                ], 404);
            }

            // Check if kelompok has jamaah
            $hasJamaah = DB::table('jamaah')->where('kelompok_id', $id)->exists();
            if ($hasJamaah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kelompok yang masih memiliki jamaah'
                ], 400);
            }

            DB::table('master_kelompok')->where('kelompok_id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kelompok berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting wilayah kelompok: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kelompok: ' . $e->getMessage()
            ], 500);
        }
    }
}

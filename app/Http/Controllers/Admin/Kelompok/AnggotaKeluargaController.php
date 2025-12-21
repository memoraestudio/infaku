<?php

namespace App\Http\Controllers\Admin\Kelompok;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnggotaKeluargaController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        $infoKelompok = DB::table('master_kelompok')
            ->where('kelompok_id', $kelompokId)
            ->first();

        return view('admin.ku.kelompok.anggota_keluarga', [
            'user' => $user,
            'info_kelompok' => $infoKelompok
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

            $query = DB::table('anggota_keluarga')
                ->join('keluarga', 'anggota_keluarga.keluarga_id', '=', 'keluarga.keluarga_id')
                ->join('jamaah', 'anggota_keluarga.jamaah_id', '=', 'jamaah.jamaah_id')
                ->where('keluarga.kelompok_id', $kelompokId);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('jamaah.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('keluarga.nama_keluarga', 'like', "%{$search}%")
                        ->orWhere('anggota_keluarga.status_hubungan', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            $data = $query->select(
                'anggota_keluarga.*',
                'keluarga.nama_keluarga',
                'jamaah.nama_lengkap',
                'jamaah.jenis_kelamin',
                'jamaah.tanggal_lahir'
            )
                ->orderBy('keluarga.nama_keluarga')
                ->orderBy('anggota_keluarga.urutan')
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
            Log::error('Error getting anggota keluarga data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data anggota keluarga'
            ], 500);
        }
    }

    public function getKeluargaOptions(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            $keluargas = DB::table('keluarga')
                ->where('kelompok_id', $kelompokId)
                ->select('keluarga_id', 'nama_keluarga')
                ->orderBy('nama_keluarga')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $keluargas
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting keluarga options: ' . $e->getMessage());
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

            // Get jamaah yang belum menjadi anggota keluarga manapun
            $jamaahs = DB::table('jamaah')
                ->leftJoin('anggota_keluarga', 'jamaah.jamaah_id', '=', 'anggota_keluarga.jamaah_id')
                ->where('jamaah.kelompok_id', $kelompokId)
                ->where('jamaah.is_aktif', true)
                ->whereNull('anggota_keluarga.jamaah_id')
                ->select('jamaah.jamaah_id', 'jamaah.nama_lengkap', 'jamaah.jenis_kelamin')
                ->orderBy('jamaah.nama_lengkap')
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

    public function getByKeluarga($keluargaId)
    {
        try {
            $anggota = DB::table('anggota_keluarga')
                ->join('jamaah', 'anggota_keluarga.jamaah_id', '=', 'jamaah.jamaah_id')
                ->where('anggota_keluarga.keluarga_id', $keluargaId)
                ->select(
                    'anggota_keluarga.*',
                    'jamaah.nama_lengkap',
                    'jamaah.jenis_kelamin',
                    'jamaah.tanggal_lahir'
                )
                ->orderBy('anggota_keluarga.urutan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $anggota
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting anggota by keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data anggota keluarga'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $anggota = DB::table('anggota_keluarga')
                ->join('keluarga', 'anggota_keluarga.keluarga_id', '=', 'keluarga.keluarga_id')
                ->join('jamaah', 'anggota_keluarga.jamaah_id', '=', 'jamaah.jamaah_id')
                ->where('anggota_keluarga.anggota_id', $id)
                ->select(
                    'anggota_keluarga.*',
                    'keluarga.nama_keluarga',
                    'jamaah.nama_lengkap',
                    'jamaah.jenis_kelamin',
                    'jamaah.tanggal_lahir'
                )
                ->first();

            if (!$anggota) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggota keluarga tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $anggota
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing anggota keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data anggota keluarga'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'keluarga_id' => 'required|exists:keluarga,keluarga_id',
                'jamaah_id' => 'required|exists:jamaah,jamaah_id',
                'status_hubungan' => 'required|in:KEPALA_KELUARGA,ISTRI,ANAK,MENANTU,CUCU,ORANGTUA,SAUDARA,LAINNYA',
                'urutan' => 'required|integer|min:1'
            ]);

            // Check if jamaah is already in a family
            $existingAnggota = DB::table('anggota_keluarga')
                ->where('jamaah_id', $validated['jamaah_id'])
                ->first();

            if ($existingAnggota) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jamaah ini sudah menjadi anggota keluarga lain'
                ], 400);
            }

            // Check if kepala keluarga already exists for this family
            if ($validated['status_hubungan'] === 'KEPALA_KELUARGA') {
                $existingKepala = DB::table('anggota_keluarga')
                    ->where('keluarga_id', $validated['keluarga_id'])
                    ->where('status_hubungan', 'KEPALA_KELUARGA')
                    ->first();

                if ($existingKepala) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Keluarga ini sudah memiliki kepala keluarga'
                    ], 400);
                }

                // Update kepala keluarga in keluarga table
                DB::table('keluarga')
                    ->where('keluarga_id', $validated['keluarga_id'])
                    ->update([
                        'kepala_keluarga_id' => $validated['jamaah_id'],
                        'updated_at' => now()
                    ]);
            }

            $validated['created_at'] = now();

            $anggotaId = DB::table('anggota_keluarga')->insertGetId($validated);

            return response()->json([
                'success' => true,
                'message' => 'Anggota keluarga berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing anggota keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan anggota keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status_hubungan' => 'required|in:KEPALA_KELUARGA,ISTRI,ANAK,MENANTU,CUCU,ORANGTUA,SAUDARA,LAINNYA',
                'urutan' => 'required|integer|min:1'
            ]);

            $anggota = DB::table('anggota_keluarga')
                ->where('anggota_id', $id)
                ->first();

            if (!$anggota) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggota keluarga tidak ditemukan'
                ], 404);
            }

            // Handle kepala keluarga change
            if ($validated['status_hubungan'] === 'KEPALA_KELUARGA' && $anggota->status_hubungan !== 'KEPALA_KELUARGA') {
                // Remove existing kepala keluarga
                DB::table('anggota_keluarga')
                    ->where('keluarga_id', $anggota->keluarga_id)
                    ->where('status_hubungan', 'KEPALA_KELUARGA')
                    ->update([
                        'status_hubungan' => 'LAINNYA',
                        'updated_at' => now()
                    ]);

                // Update keluarga table
                DB::table('keluarga')
                    ->where('keluarga_id', $anggota->keluarga_id)
                    ->update([
                        'kepala_keluarga_id' => $anggota->jamaah_id,
                        'updated_at' => now()
                    ]);
            }

            $validated['updated_at'] = now();

            DB::table('anggota_keluarga')
                ->where('anggota_id', $id)
                ->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Anggota keluarga berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating anggota keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate anggota keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $anggota = DB::table('anggota_keluarga')
                ->where('anggota_id', $id)
                ->first();

            if (!$anggota) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggota keluarga tidak ditemukan'
                ], 404);
            }

            // Prevent deletion of kepala keluarga
            if ($anggota->status_hubungan === 'KEPALA_KELUARGA') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus kepala keluarga. Ubah status terlebih dahulu.'
                ], 400);
            }

            DB::table('anggota_keluarga')
                ->where('anggota_id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Anggota keluarga berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting anggota keluarga: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus anggota keluarga: ' . $e->getMessage()
            ], 500);
        }
    }
}

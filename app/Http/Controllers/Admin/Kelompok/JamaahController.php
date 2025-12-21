<?php

namespace App\Http\Controllers\Admin\Kelompok;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JamaahController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        $infoKelompok = DB::table('master_kelompok')
            ->where('kelompok_id', $kelompokId)
            ->first();

        return view('admin.ku.kelompok.data_jamaah', [
            'user' => $user,
            'info_kelompok' => $infoKelompok
        ]);
    }

    public function print(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['id'];

        $jamaahs = DB::table('jamaah')
            ->leftJoin('keluarga', 'jamaah.jamaah_id', '=', 'keluarga.kepala_keluarga_id')
            ->leftJoin('master_dapuan', 'jamaah.dapuan_id', '=', 'master_dapuan.id')
            ->where('jamaah.id', $kelompokId)
            ->select(
                'jamaah.*',
                'keluarga.nama_keluarga',
                'master_dapuan.nama_dapuan'
            )
            ->orderBy('jamaah.nama_lengkap')
            ->get();

        $infoKelompok = DB::table('master_kelompok')
            ->where('id', $kelompokId)
            ->first();

        return view('admin.ku.kelompok.data_jamaah_print', [
            'jamaahs' => $jamaahs,
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

            $query = DB::table('jamaah')
                ->leftJoin('keluarga', 'jamaah.jamaah_id', '=', 'keluarga.kepala_keluarga_id')
                ->leftJoin('master_dapuan', 'jamaah.dapuan_id', '=', 'master_dapuan.id')
                ->where('jamaah.kelompok_id', $kelompokId);

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->Where('jamaah.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('jamaah.telepon', 'like', "%{$search}%")
                        ->orWhere('jamaah.alamat', 'like', "%{$search}%")
                        ->orWhere('keluarga.nama_keluarga', 'like', "%{$search}%")
                        ->orWhere('master_dapuan.nama_dapuan', 'like', "%{$search}%");
                });
            }

            if ($request->has('is_aktif')) {
                $query->where('jamaah.is_aktif', $request->get('is_aktif'));
            }

            $total = $query->count();
            $data = $query->select(
                'jamaah.*',
                'keluarga.nama_keluarga',
                'master_dapuan.nama_dapuan',
                'dapuan.nama_dapuan'
            )
                ->orderBy('jamaah.nama_lengkap', 'asc')
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
            Log::error('Error getting jamaah data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jamaah'
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

    public function getDapuanOptions(Request $request)
    {
        try {
            $dapuans = DB::table('master_dapuan')
                ->select('id', 'nama_dapuan')
                ->orderBy('kode_dapuan')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $dapuans
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting dapuan options: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data dapuan'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $jamaah = DB::table('jamaah')
                ->leftjoin('keluarga', 'jamaah.jamaah_id', '=', 'keluarga.kepala_keluarga_id')
                ->join('master_dapuan', 'jamaah.dapuan_id', '=', 'master_dapuan.id')
                ->where('jamaah.jamaah_id', $id)
                ->select(
                    'jamaah.*',
                    'keluarga.nama_keluarga',
                    'master_dapuan.nama_dapuan'
                )
                ->first();

            if (!$jamaah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data jamaah tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $jamaah
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing jamaah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jamaah'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['id'];
            $kodeKelompok = DB::table('master_kelompok')
                ->where('kelompok_id', $user['wilayah_id'])
                ->value('nama_kelompok');
            $kodeKelompok = $this->generateKodeKelompok($kodeKelompok);

            $validated = $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100',
                'pekerjaan' => 'nullable|string|max:100',
                'status_menikah' => 'required|in:BELUM_MENIKAH,MENIKAH,JANDA,DUDA',
                'golongan_darah' => 'nullable|in:A,B,AB,O,-',
                'dapuan_id' => 'required|exists:master_dapuan,dapuan_id',
                'is_aktif' => 'required|boolean'
            ]);

            // Generate jamaah_id
            $lastJamaah = DB::table('jamaah')
                ->where('kelompok_id', $kelompokId)
                ->count();
            $nextNumber = $lastJamaah + 1;
            $jamaahId = $kodeKelompok . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $validated['jamaah_id'] = $jamaahId;
            $validated['kelompok_id'] = $kelompokId;
            $validated['created_at'] = now();

            DB::table('jamaah')->insert($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data jamaah berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing jamaah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data jamaah: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        dd($request->all());
        try {
            $validated = $request->validate([
                'nama_lengkap' => 'required|string|max:255',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100',
                'pekerjaan' => 'nullable|string|max:100',
                'status_menikah' => 'required|in:BELUM_MENIKAH,MENIKAH,JANDA,DUDA',
                'golongan_darah' => 'nullable|in:A,B,AB,O,-',
                'dapuan_id' => 'nullable|exists:master_dapuan,dapuan_id',
                'is_aktif' => 'required|boolean'
            ]);

            $validated['updated_at'] = now();

            DB::table('jamaah')
                ->where('id', $id)
                ->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Data jamaah berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating jamaah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data jamaah: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Check if jamaah is kepala keluarga
            $isKepalaKeluarga = DB::table('keluarga')
                ->where('kepala_keluarga_id', $id)
                ->exists();

            if ($isKepalaKeluarga) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus jamaah yang menjadi kepala keluarga'
                ], 400);
            }

            // Check if jamaah has transactions
            $hasTransactions = DB::table('transaksi')
                ->where('jamaah_id', $id)
                ->exists();

            if ($hasTransactions) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus jamaah yang memiliki riwayat transaksi'
                ], 400);
            }

            // Delete from anggota_keluarga first
            DB::table('anggota_keluarga')
                ->where('jamaah_id', $id)
                ->delete();

            // Delete jamaah
            DB::table('jamaah')
                ->where('jamaah_id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data jamaah berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting jamaah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data jamaah: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateKodeKelompok(string $nama, int $limit = 4): string
    {
        $kode = '';
        $ambil = 0;

        for ($i = 0; $i < mb_strlen($nama); $i++) {
            if ($i % 2 === 0) {
                $kode .= mb_substr($nama, $i, 1);
                $ambil++;
                if ($ambil === $limit) break;
            }
        }

        return mb_strtoupper($kode);
    }
}

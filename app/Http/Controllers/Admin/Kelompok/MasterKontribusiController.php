<?php

namespace App\Http\Controllers\Admin\Kelompok;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterKontribusiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        $infoKelompok = DB::table('master_kelompok')
            ->where('kelompok_id', $kelompokId)
            ->first();

        return view('admin.ku.kelompok.master_kontribusi', [
            'user' => $user,
            'info_kelompok' => $infoKelompok
        ]);
    }

    public function getData(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            $search     = $request->get('search', '');
            // dd($search);
            $perPage    = $request->get('per_page', 10);
            $page       = $request->get('page', 1);

            $query      = DB::table('master_kontribusi')
                ->join('master_kelompok', 'master_kontribusi.created_by', '=', 'master_kelompok.kelompok_id')
                ->select(
                    'master_kontribusi.*',
                    'master_kelompok.nama_kelompok'
                )
                ->where(function ($q) use ($kelompokId) {
                    // Bisa juga menampilkan yang dibuat oleh kelompok ini
                    $q->where('created_by', $kelompokId);
                });

            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->Where('nama_kontribusi', 'like', "%{$search}%");
                });
            }

            $total = $query->count();
            $data = $query->select('*')
                ->orderBy('master_kontribusi.id', 'asc')
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
            Log::error('Error getting master kontribusi data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data master kontribusi'
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $kontribusi = DB::table('master_kontribusi')
                ->where('id', $id)
                ->first();

            if (!$kontribusi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data master kontribusi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $kontribusi
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing master kontribusi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data master kontribusi'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            $validated = $request->validate([
                'nama_kontribusi' => 'required|string|max:100',
                'keterangan' => 'nullable|string',
                'is_aktif' => 'required|boolean'
            ]);

            $kelompok = DB::table('master_kelompok')
                ->select('nama_kelompok')
                ->where('kelompok_id', $kelompokId)
                ->first();

            $urut = DB::table('master_kontribusi')
                ->where('created_by', $kelompokId)
                ->count() + 1;
            // dd($urut);

            if (DB::table('master_kelompok')->where('kelompok_id', $kelompokId)->exists()) {
                $abbr = strtoupper(
                    collect(explode(' ', $request->nama_kontribusi))
                        ->map(fn($w) => substr($w, 0, 1))
                        ->join('')
                );
                $nomorKontribusi = str_pad($urut, 3, '0', STR_PAD_LEFT)
                    . '/' . $kelompok->nama_kelompok
                    . '/' . 'IV'
                    . '/' . $abbr;
            }

            // Set level penerapan ke KELOMPOK dan created_by
            $validated['kode_kontribusi'] = strtoupper($nomorKontribusi);
            $validated['nama_kontribusi'] = strtoupper($request->nama_kontribusi);
            $validated['keterangan'] = $request->keterangan;
            $validated['created_by'] = $kelompokId;
            $validated['is_aktif'] = $request->is_aktif;
            $validated['created_at'] = now();

            $masterId = DB::table('master_kontribusi')->insertGetId($validated);

            return response()->json([
                'success' => true,
                'message' => 'Master kontribusi berhasil ditambahkan',
                'data' => ['id' => $masterId]
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing master kontribusi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan master kontribusi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'nama_kontribusi' => 'required|string|max:100',
                'keterangan' => 'nullable|string',
                'is_aktif' => 'required|boolean'
            ]);

            $validated['nama_kontribusi'] = strtoupper($request->nama_kontribusi);
            $validated['keterangan'] = $request->keterangan;
            $validated['is_aktif'] = $request->is_aktif;
            $validated['updated_at'] = now();

            $affected = DB::table('master_kontribusi')
                ->where('id', $id)
                ->update($validated);

            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data master kontribusi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Master kontribusi berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating master kontribusi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate master kontribusi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Check if master kontribusi has sub kontribusi
            $hasSubKontribusi = DB::table('sub_kontribusi')
                ->where('id', $id)
                ->exists();

            if ($hasSubKontribusi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus master kontribusi yang memiliki sub kontribusi'
                ], 400);
            }

            // Check if master kontribusi is used in transactions via kategori_keuangan
            $isUsed = DB::table('kategori_keuangan')
                ->where('jenis_ibadah', function ($query) use ($id) {
                    $query->select('nama_kontribusi')
                        ->from('master_kontribusi')
                        ->where('id', $id);
                })
                ->exists();

            if ($isUsed) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus master kontribusi yang sudah digunakan dalam transaksi'
                ], 400);
            }

            $affected = DB::table('master_kontribusi')
                ->where('id', $id)
                ->delete();

            if ($affected === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data master kontribusi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Master kontribusi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting master kontribusi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus master kontribusi: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin\Kelompok;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class JamaahController extends Controller
{
    // ============================================
    // SECTION 1: VIEW METHODS
    // ============================================

    /**
     * Display main jamaah view
     */
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

    /**
     * Display print view
     */
    public function print(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        $jamaahs = DB::table('jamaah')
            ->leftJoin('keluarga', 'jamaah.jamaah_id', '=', 'keluarga.kepala_keluarga_id')
            ->leftJoin('master_dapuan', 'jamaah.dapuan_id', '=', 'master_dapuan.id')
            // ->where('jamaah.id', $kelompokId)
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

    // ============================================
    // SECTION 2: API METHODS
    // ============================================

    /**
     * Get paginated jamaah data
     */
    public function getData(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            // Get query parameters
            $search = $request->get('search', '');
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $isAktif = $request->get('is_aktif');

            // Build query
            $query = DB::table('jamaah')
                ->leftJoin('keluarga', 'jamaah.jamaah_id', '=', 'keluarga.kepala_keluarga_id')
                ->Join('roles', 'jamaah.dapuan_id', '=', 'roles.role_id')
                ->where('jamaah.kelompok_id', $kelompokId);

            // Apply search
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->Where('jamaah.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('jamaah.telepon', 'like', "%{$search}%")
                        ->orWhere('jamaah.alamat', 'like', "%{$search}%")
                        ->orWhere('keluarga.nama_keluarga', 'like', "%{$search}%")
                        ->orWhere('roles.nama_role', 'like', "%{$search}%");
                });
            }

            // Apply status filter
            if ($isAktif !== null) {
                $query->where('jamaah.is_aktif', $isAktif);
            }

            // Get total count
            $total = $query->count();

            // Get paginated data
            $data = $query->select(
                'jamaah.*',
                'keluarga.nama_keluarga',
                'roles.nama_role'
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
                'message' => 'Terjadi kesalahan saat memuat data'
            ], 500);
        }
    }

    /**
     * Get single jamaah data
     */
    public function show($id)
    {
        try {
            $jamaah = DB::table('jamaah')
                ->leftJoin('keluarga', 'jamaah.id', '=', 'keluarga.kepala_keluarga_id')
                ->join('roles', 'jamaah.dapuan_id', '=', 'roles.role_id')
                ->where('jamaah.id', $id)
                ->join('users', 'jamaah.jamaah_id', '=', 'users.jamaah_id')
                ->select(
                    'jamaah.*',
                    'users.email',
                    'keluarga.nama_keluarga',
                    'roles.nama_role',
                    'roles.role_id as dapuan_id'
                )
                ->first();

            if (!$jamaah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data jamaah tidak ditemukan'
                ], 404);
            }

            // Format response data
            $data = $this->formatJamaahResponse($jamaah);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing jamaah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jamaah'
            ], 500);
        }
    }

    /**
     * Store new jamaah
     */
    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required|string|max:255',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:15',
                'pekerjaan' => 'nullable|string|max:100',
                'status_menikah' => 'required|in:Belum Menikah,Menikah,Janda,Duda',
                'golongan_darah' => 'nullable|in:A,B,AB,O,-',
                'dapuan_id' => 'required',
                'is_aktif' => 'required|boolean'
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

            // Generate jamaah ID
            $jamaahId = $this->generateJamaahId($kelompokId);
            // Mulai transaction
            DB::beginTransaction();

            try {
                // 1. Insert ke tabel jamaah menggunakan Query Builder
                $jamaahData = [
                    'nama_lengkap' => $request->input('nama_lengkap'),
                    'tempat_lahir' => $request->input('tempat_lahir'),
                    'tanggal_lahir' => $request->input('tanggal_lahir'),
                    'jenis_kelamin' => $request->input('jenis_kelamin'),
                    'golongan_darah' => $request->input('golongan_darah'),
                    'status_menikah' => $request->input('status_menikah'),
                    'pekerjaan' => $request->input('pekerjaan'),
                    'telepon' => $request->input('telepon'),
                    'alamat' => $request->input('alamat'),
                    'dapuan_id' => $request->input('dapuan_id'),
                    'is_aktif' => $request->input('is_aktif', true),
                    'jamaah_id' => $jamaahId,
                    'kelompok_id' => $kelompokId,
                    'created_at' => DB::raw('NOW()')
                ];

                DB::table('jamaah')->insert($jamaahData);

                // 2. Generate username dan email
                $username = $this->generateUsername($request->input('nama_lengkap'));
                $email = $this->generateEmail($request->input('nama_lengkap'));

                // Cek duplikasi username/email
                $counter = 1;
                $originalUsername = $username;
                $originalEmail = $email;

                // Query Builder untuk cek duplikasi
                while (
                    DB::table('users')->where('username', $username)->exists() ||
                    DB::table('users')->where('email', $email)->exists()
                ) {
                    $username = $originalUsername . $counter;
                    $email = str_replace('@infaku.com', $counter . '@infaku.com', $originalEmail);
                    $counter++;
                }

                // 3. Insert ke tabel users menggunakan Query Builder
                $userData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => bcrypt('12345'),
                    'role_id' => $request->input('dapuan_id'),
                    'jamaah_id' => $jamaahId, // Menggunakan jamaah_id (RJGL187)
                    'is_aktif' => $request->input('is_aktif', true),
                    'last_login' => null,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()')
                ];

                DB::table('users')->insert($userData);

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data jamaah berhasil ditambahkan',
                    'data' => [
                        'jamaah_id' => $jamaahId,
                        'username' => $username,
                        'email' => $email,
                        'password_default' => 'rajagaluh123'
                    ]
                ]);
            } catch (\Exception $e) {
                // Rollback jika ada error
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            // Log error dengan Query Builder style
            Log::error('Error Menambah jamaah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambah data jamaah'
            ], 500);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data jamaah',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan internal'
            ], 500);
        }
    }

    /**
     * Generate username dari nama lengkap menggunakan Query Builder style
     */
    private function generateUsername($namaLengkap)
    {
        $namaArray = explode(' ', trim($namaLengkap));
        $namaDepan = strtolower($namaArray[0]);

        if (count($namaArray) > 1) {
            $namaBelakang = strtolower($namaArray[count($namaArray) - 1]);
            $username = $namaDepan . $namaBelakang;
        } else {
            $username = $namaDepan;
        }

        // Hapus karakter non-alphanumeric
        $username = preg_replace('/[^a-z0-9]/', '', $username);

        return $username;
    }

    /**
     * Generate email dari nama lengkap
     */
    private function generateEmail($namaLengkap)
    {
        $username = $this->generateUsername($namaLengkap);
        return $username . '@infaku.com';
    }

    /**
     * Generate jamaah ID menggunakan Query Builder
     */
    private function generateJamaahId($kelompokId)
    {
        $prefix = 'RJL';

        // Cek nomor urut terakhir (tidak peduli kelompok_id, karena formatnya RJL001, RJL002, dst)
        $lastJamaah = DB::table('jamaah')
            ->where('jamaah_id', 'LIKE', $prefix . '%')
            ->orderByRaw('CAST(SUBSTRING(jamaah_id, 4) AS UNSIGNED) DESC')
            ->first();

        if ($lastJamaah && !empty($lastJamaah->jamaah_id)) {
            // Ambil angka setelah prefix "RJL"
            $lastId = $lastJamaah->jamaah_id;
            $lastNumber = (int) substr($lastId, 3); // Ambil setelah 3 karakter "RJL"
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber; // Contoh: RJL001, RJL002, RJL003, dst.
    }

    /**
     * Update jamaah data
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'nama_lengkap' => 'required|string|max:255',
                'tempat_lahir' => 'nullable|string|max:100',
                'tanggal_lahir' => 'nullable|date',
                'jenis_kelamin' => 'required|in:L,P',
                'alamat' => 'nullable|string',
                'telepon' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:100',
                'pekerjaan' => 'nullable|string|max:100',
                'status_menikah' => 'required|in:Belum Menikah,Menikah,Janda,Duda',
                'golongan_darah' => 'nullable|in:A,B,AB,O,-',
                'dapuan_id' => 'nullable',
                'is_aktif' => 'required|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // 1. Ambil data jamaah saat ini
            $currentJamaah = DB::table('jamaah')
                ->where('id', $id)
                ->first(['jamaah_id', 'nama_lengkap']);

            if (!$currentJamaah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data jamaah tidak ditemukan'
                ], 404);
            }

            $jamaahId = $currentJamaah->jamaah_id;
            $oldNamaLengkap = $currentJamaah->nama_lengkap;
            $newNamaLengkap = $request->input('nama_lengkap');

            // 2. Update data jamaah
            $jamaahUpdateData = [
                'nama_lengkap' => $newNamaLengkap,
                'tempat_lahir' => $request->input('tempat_lahir'),
                'tanggal_lahir' => $request->input('tanggal_lahir'),
                'jenis_kelamin' => $request->input('jenis_kelamin'),
                'alamat' => $request->input('alamat'),
                'telepon' => $request->input('telepon'),
                'pekerjaan' => $request->input('pekerjaan'),
                'status_menikah' => $request->input('status_menikah'),
                'golongan_darah' => $request->input('golongan_darah'),
                'dapuan_id' => $request->input('dapuan_id'),
                'is_aktif' => $request->input('is_aktif') ? 1 : 0,
                'updated_at' => now()
            ];

            DB::table('jamaah')
                ->where('id', $id)
                ->update($jamaahUpdateData);

            // 3. Update user jika ada
            $userExists = DB::table('users')
                ->where('jamaah_id', $id)
                ->exists();

            if ($userExists) {
                $userUpdateData = [
                    'is_aktif' => $request->input('is_aktif') ? 1 : 0,
                    'updated_at' => now()
                ];

                // Update email jika ada
                if ($request->has('email') && $request->input('email')) {
                    $userUpdateData['email'] = $request->input('email');
                }

                // Update username jika nama berubah
                if ($newNamaLengkap !== $oldNamaLengkap) {
                    $newUsername = $this->generateUsername($newNamaLengkap);

                    // Cek duplikasi username
                    $duplicateUsername = DB::table('users')
                        ->where('username', $newUsername)
                        ->where('jamaah_id', '!=', $jamaahId)
                        ->exists();

                    if (!$duplicateUsername) {
                        $userUpdateData['username'] = $newUsername;
                    }
                }

                DB::table('users')
                    ->where('jamaah_id', $jamaahId)
                    ->update($userUpdateData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data jamaah berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating jamaah: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data jamaah'
            ], 500);
        }
    }

    /**
     * Delete jamaah data
     */
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

            // Delete from related tables
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
                'message' => 'Gagal menghapus data jamaah'
            ], 500);
        }
    }

    // ============================================
    // SECTION 3: OPTION METHODS
    // ============================================

    /**
     * Get dapuan options
     */
    public function getDapuanOptions(Request $request)
    {
        try {
            $dapuans = DB::table('roles')
                ->select('role_id', 'nama_role', 'level')
                ->orderBy('role_id')
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

    /**
     * Get keluarga options
     */
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

    // ============================================
    // SECTION 4: HELPER METHODS
    // ============================================

    /**
     * Format jamaah response
     */
    private function formatJamaahResponse($jamaah)
    {
        // Format tanggal_lahir
        $tanggal_lahir = ($jamaah->tanggal_lahir && $jamaah->tanggal_lahir !== '0000-01-01')
            ? date('Y-m-d', strtotime($jamaah->tanggal_lahir))
            : null;
        return [
            'id' => $jamaah->id,
            'jamaah_id' => $jamaah->jamaah_id,
            'nik' => $jamaah->nik,
            'nama_lengkap' => $jamaah->nama_lengkap,
            'tempat_lahir' => $jamaah->tempat_lahir,
            'tanggal_lahir' => $tanggal_lahir,
            'jenis_kelamin' => $jamaah->jenis_kelamin,
            'alamat' => $jamaah->alamat,
            'telepon' => $jamaah->telepon,
            'email' => $jamaah->email,
            'pekerjaan' => $jamaah->pekerjaan,
            'status_menikah' => $jamaah->status_menikah,
            'golongan_darah' => $jamaah->golongan_darah,
            'dapuan_id' => isset($jamaah->dapuan_id) ? (string)$jamaah->dapuan_id : null,
            'nama_dapuan' => $jamaah->nama_role,
            'nama_keluarga' => $jamaah->nama_keluarga,
            'is_aktif' => (bool)$jamaah->is_aktif,
            'created_at' => $jamaah->created_at,
            'updated_at' => $jamaah->updated_at,
            'kelompok_id' => $jamaah->kelompok_id,
            'foto_profil' => $jamaah->foto_profil,
        ];
    }

    /**
     * Generate kode kelompok
     */
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

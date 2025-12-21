<?php

namespace App\Http\Controllers\Admin\Kelompok;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RiwayatTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        $infoKelompok = DB::table('master_kelompok')
            ->where('kelompok_id', $kelompokId)
            ->first();

        return view('admin.ku.kelompok.riwayat-transaksi', [
            'user' => $user,
            'info_kelompok' => $infoKelompok
        ]);
    }

    public function getData(Request $request)
    {
        // dd('masuk');
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            $search = $request->get('search', '');
            $startDate = $request->get('start_date', '');
            $endDate = $request->get('end_date', '');
            $status = $request->get('status', '');
            $metodeBayar = $request->get('metode_bayar', '');
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            $query = DB::table('transaksi')
                ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
                ->where('jamaah.kelompok_id', $kelompokId)
                ->select(
                    'transaksi.*',
                    'jamaah.nama_lengkap as nama_jamaah',
                );

            // Search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('transaksi.kode_transaksi', 'like', "%{$search}%")
                        ->orWhere('transaksi.transaksi_id', 'like', "%{$search}%")
                        ->orWhere('jamaah.nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('transaksi.keterangan', 'like', "%{$search}%");
                });
            }

            // Date filter
            if (!empty($startDate)) {
                $query->whereDate('transaksi.tgl_transaksi', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('transaksi.tgl_transaksi', '<=', $endDate);
            }

            // Status filter
            if (!empty($status) && $status !== 'ALL') {
                $query->where('transaksi.status', $status);
            }

            // Metode bayar filter
            if (!empty($metodeBayar) && $metodeBayar !== 'ALL') {
                $query->where('transaksi.metode_bayar', $metodeBayar);
            }

            $total = $query->count();
            $data = $query->orderBy('transaksi.created_at', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();

            // Parse data_json for each transaction
            $data->each(function ($item) {
                $item->data_json_parsed = json_decode($item->data_json, true);
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'current_page' => (int)$page,
                'last_page' => ceil($total / $perPage),
                'total' => $total,
                'summary' => $this->getSummary($query)
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting riwayat transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data riwayat transaksi'
            ], 500);
        }
    }

    private function getSummary($query)
    {
        $cloneQuery = clone $query;
        return [
            'total_transaksi' => $cloneQuery->count(),
            'total_pendapatan' => $cloneQuery->sum('transaksi.jumlah'),
            'total_verified' => $cloneQuery->clone()->where('transaksi.status', 'VERIFIED')->sum('transaksi.jumlah'),
            'total_pending' => $cloneQuery->clone()->where('transaksi.status', 'PENDING')->sum('transaksi.jumlah')
        ];
    }

    public function show($id)
    {
        try {
            $transaksi = DB::table('transaksi')
                ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
                ->where('transaksi.transaksi_id', $id)
                ->select(
                    'transaksi.*',
                    'jamaah.nama_lengkap as nama_jamaah',
                    'jamaah.nik',
                    'jamaah.telepon',
                    'jamaah.alamat',
                    'jamaah.foto_profil',
                )
                ->first();

            if (!$transaksi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data transaksi tidak ditemukan'
                ], 404);
            }

            // Parse data_json
            $transaksi->data_json_parsed = json_decode($transaksi->data_json, true);

            return response()->json([
                'success' => true,
                'data' => $transaksi
            ]);
        } catch (\Exception $e) {
            Log::error('Error showing transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail transaksi'
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $user = $request->session()->get('user');

            $validated = $request->validate([
                'status' => 'required|in:VERIFIED,REJECTED',
                'catatan' => 'nullable|string|max:500'
            ]);

            $updateData = [
                'status' => $validated['status'],
                'verified_by' => $user['user_id'],
                'verified_at' => now(),
                'keterangan' => $validated['catatan'] ?? null
            ];

            DB::table('transaksi')
                ->where('transaksi_id', $id)
                ->update($updateData);

            // Log activity
            DB::table('activity_logs')->insert([
                'user_id' => $user['user_id'],
                'action' => 'UPDATE_STATUS_TRANSAKSI',
                'description' => 'Update status transaksi ' . $id . ' menjadi ' . $validated['status'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'created_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Status transaksi berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating transaksi status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            $startDate = $request->get('start_date', '');
            $endDate = $request->get('end_date', '');
            $status = $request->get('status', '');

            $query = DB::table('transaksi')
                ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
                ->leftJoin('users', 'transaksi.created_by', '=', 'users.id')
                ->where('jamaah.kelompok_id', $kelompokId)
                ->select(
                    'transaksi.kode_transaksi',
                    'transaksi.tgl_transaksi',
                    'jamaah.nama_lengkap as nama_jamaah',
                    'jamaah.nik',
                    'transaksi.kategori_id',
                    'transaksi.jumlah',
                    'transaksi.metode_bayar',
                    'transaksi.status',
                    'transaksi.keterangan',
                    'users.name as created_by',
                    'transaksi.created_at'
                );

            if (!empty($startDate)) {
                $query->whereDate('transaksi.tgl_transaksi', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('transaksi.tgl_transaksi', '<=', $endDate);
            }
            if (!empty($status) && $status !== 'ALL') {
                $query->where('transaksi.status', $status);
            }

            $data = $query->orderBy('transaksi.created_at', 'desc')->get();

            $filename = 'riwayat-transaksi-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Kode Transaksi',
                    'Tanggal',
                    'Nama Jamaah',
                    'NIK',
                    'Kategori',
                    'Jumlah',
                    'Metode Bayar',
                    'Status',
                    'Keterangan',
                    'Dibuat Oleh',
                    'Waktu Input'
                ]);

                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->kode_transaksi,
                        $row->tgl_transaksi,
                        $row->nama_jamaah,
                        $row->nik,
                        $row->kategori_id,
                        number_format($row->jumlah, 2),
                        $row->metode_bayar,
                        $row->status,
                        $row->keterangan,
                        $row->created_by,
                        $row->created_at
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            Log::error('Error exporting transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal export data'
            ], 500);
        }
    }

    public function print(Request $request)
    {
        try {
            $user = $request->session()->get('user');
            $kelompokId = $user['wilayah_id'];

            $startDate = $request->get('start_date', '');
            $endDate = $request->get('end_date', '');
            $status = $request->get('status', '');

            $query = DB::table('transaksi')
                ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
                ->leftJoin('users', 'transaksi.created_by', '=', 'users.id')
                ->where('jamaah.kelompok_id', $kelompokId)
                ->select(
                    'transaksi.*',
                    'jamaah.nama_lengkap as nama_jamaah',
                    'jamaah.nik',
                    'users.name as created_by_name'
                );

            if (!empty($startDate)) {
                $query->whereDate('transaksi.tgl_transaksi', '>=', $startDate);
            }
            if (!empty($endDate)) {
                $query->whereDate('transaksi.tgl_transaksi', '<=', $endDate);
            }
            if (!empty($status) && $status !== 'ALL') {
                $query->where('transaksi.status', $status);
            }

            $transaksis = $query->orderBy('transaksi.created_at', 'desc')->get();

            $infoKelompok = DB::table('master_kelompok')
                ->where('kelompok_id', $kelompokId)
                ->first();

            $summary = [
                'total_transaksi' => $transaksis->count(),
                'total_pendapatan' => $transaksis->sum('jumlah'),
                'total_verified' => $transaksis->where('status', 'VERIFIED')->sum('jumlah'),
                'total_pending' => $transaksis->where('status', 'PENDING')->sum('jumlah')
            ];

            return view('admin.ku.kelompok.riwayat-transaksi-print', [
                'transaksis' => $transaksis,
                'info_kelompok' => $infoKelompok,
                'user' => $user,
                'summary' => $summary,
                'filters' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => $status
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error printing transaksi: ' . $e->getMessage());
            return back()->with('error', 'Gagal mencetak data');
        }
    }
}

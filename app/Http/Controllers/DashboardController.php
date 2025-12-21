<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->session()->get('user');
        $data = [];

        // Data berdasarkan role
        switch ($user['role_id']) {
            case 'RL001': // Pusat
                $data = $this->getDashboardPusat();
                break;
            case 'RL002': // Daerah
                $data = $this->getDashboardDaerah($user['wilayah_id']);
                break;
            case 'RL003': // Desa
                $data = $this->getDashboardDesa($user['wilayah_id']);
                break;
            case 'RL004': // Kelompok
                return $this->kelompok($request); // Redirect ke dashboard kelompok
            default:
                $data = $this->getDefaultDashboard();
        }

        return view('admin.ku.kelompok.dashboard', $data);
    }

    public function kelompok(Request $request)
    {
        $user = $request->session()->get('user');
        $data = $this->getDashboardKelompok($user['wilayah_id']);
        $data['user'] = $user; // Tambahkan user data ke view

        return view('admin.ku.kelompok.dashboard', $data);
    }

    public function ruyah()
    {
        $user = Auth::user();
        $data = $this->getDashboardRuyah($user->jamaah_id);
        return view('ruyah.dashboard', $data);
    }

    private function getDashboardPusat()
    {
        return [
            'total_daerah' => DB::table('master_daerah')->count(),
            'total_desa' => DB::table('master_desa')->count(),
            'total_kelompok' => DB::table('master_kelompok')->count(),
            'total_jamaah' => DB::table('jamaah')->where('is_aktif', true)->count(),
            'recent_activities' => DB::table('activity_logs')
                ->join('users', 'activity_logs.user_id', '=', 'users.user_id')
                ->orderBy('activity_logs.created_at', 'desc')
                ->limit(5)
                ->get(['activity_logs.*', 'users.nama_lengkap'])
        ];
    }

    private function getDashboardKelompok($kelompokId)
    {
        // Info kelompok
        $infoKelompok = DB::table('master_kelompok')
            ->where('kelompok_id', $kelompokId)
            ->first();

        return [
            'info_kelompok' => $infoKelompok,
            'total_keluarga' => DB::table('keluarga')->where('kelompok_id', $kelompokId)->count(),
            'total_jamaah' => DB::table('jamaah')->where('kelompok_id', $kelompokId)->where('is_aktif', true)->count(),
            'total_transaksi_bulan_ini' => DB::table('transaksi')
                ->where('status', 'VERIFIED')
                ->whereMonth('tgl_transaksi', now()->month)
                ->whereYear('tgl_transaksi', now()->year)
                ->count(),
            'pemasukan_bulan_ini' => DB::table('transaksi')
                ->join('master_kontribusi', 'transaksi.kategori_id', '=', 'master_kontribusi.kontribusi_id')
                ->where('transaksi.status', 'VERIFIED')
                ->where('master_kontribusi.tipe_kategori', 'PEMASUKAN')
                ->whereMonth('transaksi.tgl_transaksi', now()->month)
                ->whereYear('transaksi.tgl_transaksi', now()->year)
                ->sum('transaksi.jumlah') ?? 0,
            'recent_transactions' => DB::table('transaksi')
                ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
                ->join('master_kontribusi', 'transaksi.kategori_id', '=', 'master_kontribusi.kontribusi_id')
                ->where('transaksi.status', 'VERIFIED')
                ->orderBy('transaksi.created_at', 'desc')
                ->limit(5)
                ->get(['transaksi.*', 'jamaah.nama_lengkap', 'master_kontribusi.nama_kontribusi'])
        ];
    }

    private function getDashboardRuyah($jamaahId)
    {
        return [
            'total_transaksi' => DB::table('transaksi')->where('jamaah_id', $jamaahId)->count(),
            'total_belanja_bulan_ini' => DB::table('transaksi')
                ->where('jamaah_id', $jamaahId)
                ->where('status', 'VERIFIED')
                ->whereMonth('tgl_transaksi', now()->month)
                ->whereYear('tgl_transaksi', now()->year)
                ->sum('jumlah'),
            'recent_transactions' => DB::table('transaksi')
                ->join('master_kontribusi', 'transaksi.kategori_id', '=', 'master_kontribusi.kontribusi_id')
                ->where('transaksi.jamaah_id', $jamaahId)
                ->where('transaksi.status', 'VERIFIED')
                ->orderBy('transaksi.created_at', 'desc')
                ->limit(5)
                ->get(['transaksi.*', 'master_kontribusi.nama_kontribusi'])
        ];
    }

    public function getStats(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        try {
            Log::info('Loading stats for kelompok: ' . $kelompokId);

            $stats = [
                'total_kas' => $this->getTotalKas($kelompokId),
                'kas_bulan_ini' => $this->getKasBulanIni($kelompokId),
                'total_sodaqoh' => $this->getTotalSodaqoh($kelompokId),
                'total_jamaah' => $this->getTotalJamaah($kelompokId),
                'total_keluarga' => $this->getTotalKeluarga($kelompokId),
                'transaksi_bulan_ini' => $this->getTransaksiBulanIni($kelompokId),
            ];

            Log::info('Stats loaded successfully', $stats);

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading stats: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat statistik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API untuk data chart kontribusi
     */
    public function getChartData(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        try {
            Log::info('Loading chart data for kelompok: ' . $kelompokId);

            $chartData = $this->getChartDataForKelompok($kelompokId);

            Log::info('Chart data loaded successfully', $chartData);

            return response()->json([
                'success' => true,
                'chart' => $chartData
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading chart: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data chart: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API untuk aktivitas terbaru
     */
    public function getActivities(Request $request)
    {
        $user = $request->session()->get('user');
        $kelompokId = $user['wilayah_id'];

        try {
            Log::info('Loading activities for kelompok: ' . $kelompokId);

            $activities = $this->getRecentActivities($kelompokId);

            Log::info('Activities loaded successfully', ['count' => count($activities)]);

            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading activities: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat aktivitas: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper methods untuk query data - DIPERBAIKI
    private function getTotalKas($kelompokId)
    {
        return DB::table('transaksi')
            ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
            ->join('master_kontribusi', 'transaksi.kategori_id', '=', 'master_kontribusi.kontribusi_id')
            ->where('jamaah.kelompok_id', $kelompokId)
            ->where('transaksi.status', 'VERIFIED')
            ->where('master_kontribusi.tipe_kategori', 'PEMASUKAN')
            ->sum('transaksi.jumlah') ?? 0;
    }

    private function getKasBulanIni($kelompokId)
    {
        return DB::table('transaksi')
            ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
            ->join('master_kontribusi', 'transaksi.kategori_id', '=', 'master_kontribusi.kontribusi_id')
            ->where('jamaah.kelompok_id', $kelompokId)
            ->where('transaksi.status', 'VERIFIED')
            ->where('master_kontribusi.tipe_kategori', 'PEMASUKAN')
            ->whereMonth('transaksi.tgl_transaksi', now()->month)
            ->whereYear('transaksi.tgl_transaksi', now()->year)
            ->sum('transaksi.jumlah') ?? 0;
    }

    private function getTotalSodaqoh($kelompokId)
    {
        return DB::table('transaksi')
            ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
            ->join('master_kontribusi', 'transaksi.kategori_id', '=', 'master_kontribusi.kontribusi_id')
            ->where('jamaah.kelompok_id', $kelompokId)
            ->where('transaksi.status', 'VERIFIED')
            ->where('master_kontribusi.jenis_ibadah', 'SODAQOH')
            ->sum('transaksi.jumlah') ?? 0;
    }

    private function getTotalJamaah($kelompokId)
    {
        return DB::table('jamaah')
            ->where('kelompok_id', $kelompokId)
            ->where('is_aktif', true)
            ->count();
    }

    private function getTotalKeluarga($kelompokId)
    {
        return DB::table('keluarga')
            ->where('kelompok_id', $kelompokId)
            ->count();
    }

    private function getTransaksiBulanIni($kelompokId)
    {
        return DB::table('transaksi')
            ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
            ->where('jamaah.kelompok_id', $kelompokId)
            ->where('transaksi.status', 'VERIFIED')
            ->whereMonth('transaksi.tgl_transaksi', now()->month)
            ->whereYear('transaksi.tgl_transaksi', now()->year)
            ->count();
    }

    private function getChartDataForKelompok($kelompokId)
    {
        // Data kontribusi 6 bulan terakhir
        $months = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthYear = $date->format('M Y');
            $months[] = $monthYear;

            $total = DB::table('transaksi')
                ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
                ->join('master_kontribusi', 'transaksi.kategori_id', '=', 'master_kontribusi.kontribusi_id')
                ->where('jamaah.kelompok_id', $kelompokId)
                ->where('transaksi.status', 'VERIFIED')
                ->where('master_kontribusi.tipe_kategori', 'PEMASUKAN')
                ->whereMonth('transaksi.tgl_transaksi', $date->month)
                ->whereYear('transaksi.tgl_transaksi', $date->year)
                ->sum('transaksi.jumlah') ?? 0;

            $data[] = $total;
        }

        return [
            'labels' => $months,
            'data' => $data
        ];
    }

    private function getRecentActivities($kelompokId)
    {
        $activities = DB::table('transaksi')
            ->join('jamaah', 'transaksi.jamaah_id', '=', 'jamaah.jamaah_id')
            ->join('master_kontribusi', 'transaksi.kategori_id', '=', 'master_kontribusi.kontribusi_id')
            ->where('jamaah.kelompok_id', $kelompokId)
            ->where('transaksi.status', 'VERIFIED')
            ->orderBy('transaksi.created_at', 'desc')
            ->limit(5)
            ->get(['transaksi.*', 'jamaah.nama_lengkap', 'master_kontribusi.nama_kontribusi', 'master_kontribusi.tipe_kategori'])
            ->map(function ($item) {
                $type = $item->tipe_kategori === 'PEMASUKAN' ? 'payment' : 'transaction';
                $icon = $item->tipe_kategori === 'PEMASUKAN' ? 'eg-inbox' : 'eg-chart';

                return [
                    'type' => $type,
                    'icon' => $icon,
                    'description' => $item->nama_lengkap . ' - ' . $item->nama_kontribusi . ' (Rp ' . number_format($item->jumlah, 0, ',', '.') . ')',
                    'created_at' => $item->created_at
                ];
            })
            ->toArray();

        // Jika tidak ada transaksi, beri pesan default
        if (empty($activities)) {
            return [
                [
                    'type' => 'info',
                    'icon' => 'eg-info',
                    'description' => 'Belum ada transaksi terbaru',
                    'created_at' => now()->toDateTimeString()
                ]
            ];
        }

        return $activities;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);

        // Debug: Cek apakah user ada
        $user = DB::table('users')
            ->where('email', $request->email)
            ->join('jamaah', 'users.jamaah_id', '=', 'jamaah.id')
            ->join('master_kelompok', 'jamaah.kelompok_id', '=', 'master_kelompok.id')
            ->select(
                'users.*',
                'jamaah.id as jamaah_id',
                'jamaah.nama_lengkap',
                'jamaah.kelompok_id',
                'master_kelompok.nama_kelompok'
            )
            ->first();
        // dd($user);

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        // Debug: Cek password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password salah.']);
        }

        // Manual login karena struktur database mungkin berbeda
        $userData = [
            'user_id' => $user->jamaah_id,
            'username' => $user->username,
            'email' => $user->email,
            'nama_lengkap' => $user->nama_lengkap,
            'role_id' => $user->role_id,
            'nama_kelompok' => $user->nama_kelompok,
            'wilayah_id' => $user->kelompok_id,
            'jamaah_id' => $user->jamaah_id
        ];

        session(['user' => $userData]);

        // Redirect berdasarkan role
        $redirectMap = [
            'RL001' => 'admin.dashboard', // Pusat
            'RL002' => 'admin.dashboard', // Daerah  
            'RL003' => 'admin.dashboard', // Desa
            'RL004' => 'admin.kelompok.dashboard', // Kelompok
            'RL005' => 'ruyah.dashboard', // Ruyah
        ];

        if (array_key_exists($user->role_id, $redirectMap)) {
            return redirect()->route($redirectMap[$user->role_id]);
        }

        return redirect('/dashboard');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'telepon' => 'required'
        ]);

        // Generate username dari nama
        $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $request->nama_lengkap));

        // Cek jika username sudah ada
        $existingUser = DB::table('users')->where('username', $username)->first();
        if ($existingUser) {
            $username = $username . rand(100, 999);
        }

        DB::table('users')->insert([
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'telepon' => $request->telepon,
            'role_id' => 'RL005', // Default role Ruyah
            'is_aktif' => true,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('user');
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

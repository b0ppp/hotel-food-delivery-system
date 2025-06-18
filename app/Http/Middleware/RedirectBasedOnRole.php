<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Butuh ini untuk mengecek user
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dapatkan data pengguna yang sedang login
        $user = Auth::user();

        // Asumsi: model User Anda memiliki kolom/properti 'role'
        // Cek jika pengguna punya peran 'admin'
        if ($user && $user->role === 'admin') {
            // Jika ya, langsung alihkan ke rute dashboard admin.
            // Kode di bawah ini (return $next) tidak akan pernah tercapai.
            return redirect()->route('admin.dashboard');
        }
        
        // Cek jika pengguna punya peran 'receptionist'
        if ($user && $user->role === 'receptionist') {
            // Alihkan ke dashboard resepsionis (atau halaman relevan lainnya)
            return redirect()->route('receptionist.dashboard'); // Pastikan rute ini ada
        }
        
        // Cek jika pengguna punya peran 'kitchen'
        if ($user && $user->role === 'kitchen') {
            // Alihkan ke dashboard dapur
            return redirect()->route('kitchen.dashboard');
        }

        // Cek jika pengguna punya peran 'delivery'
        if ($user && $user->role === 'delivery') {
            // Alihkan ke dashboard staf antar
            return redirect()->route('delivery.dashboard');
        }

        // --- INI BAGIAN PENTING ---
        // Jika tidak ada dari kondisi di atas yang terpenuhi (misal: user biasa tanpa peran),
        // maka izinkan permintaan untuk melanjutkan ke tujuan aslinya (yaitu ke 'return view('dashboard')').
        return $next($request);
    }
}
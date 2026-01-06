<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class StrictRoleAccess
{
    /**
     * Handle an incoming request.
     * 
     * Strict protection: Pustakawan cannot access Admin-only routes
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $currentPath = $request->path();

        // Admin-only routes (librarians management)
        $adminOnlyRoutes = [
            'admin/librarians',
            'admin/librarians/create',
            'admin/librarians/*/edit',
        ];

        // Check if Pustakawan trying to access Admin-only routes
        if ($user->role === User::ROLES['Librarian']) {
            foreach ($adminOnlyRoutes as $pattern) {
                if ($this->matchesPattern($currentPath, $pattern)) {
                    return redirect()
                        ->route('pustakawan.dashboard')
                        ->with('error', '⛔ Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.');
                }
            }

            // Also block if trying to access /admin/* prefix
            if (str_starts_with($currentPath, 'admin/')) {
                return redirect()
                    ->route('pustakawan.dashboard')
                    ->with('error', '⛔ Akses ditolak! Halaman ini hanya untuk Administrator.');
            }
        }

        // Check if Member trying to access admin/pustakawan routes
        if ($user->role === User::ROLES['Member']) {
            if (str_starts_with($currentPath, 'admin/') || str_starts_with($currentPath, 'pustakawan/')) {
                return redirect()
                    ->route('home')
                    ->with('error', '⛔ Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.');
            }
        }

        return $next($request);
    }

    /**
     * Check if path matches pattern (supports wildcard *)
     */
    protected function matchesPattern($path, $pattern)
    {
        $pattern = str_replace('*', '.*', $pattern);
        return preg_match('#^' . $pattern . '$#', $path);
    }
}
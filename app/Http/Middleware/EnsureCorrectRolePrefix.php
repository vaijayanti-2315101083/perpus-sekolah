<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureCorrectRolePrefix
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip if user is not authenticated
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $currentPath = $request->path(); // e.g. "admin/dashboard" or "pustakawan/books"
        
        // Get correct prefix for current user's role
        $correctPrefix = role_prefix(); // Returns 'admin' or 'pustakawan' based on role
        
        // Define all possible admin prefixes
        $adminPrefixes = ['admin', 'pustakawan'];
        
        // Check if current path starts with any admin prefix
        $currentPrefix = null;
        foreach ($adminPrefixes as $prefix) {
            if (str_starts_with($currentPath, $prefix . '/')) {
                $currentPrefix = $prefix;
                break;
            }
        }
        
        // If no admin prefix found, this is a member route - allow
        if ($currentPrefix === null) {
            return $next($request);
        }
        
        // If current prefix matches correct prefix, allow
        if ($currentPrefix === $correctPrefix) {
            return $next($request);
        }
        
        // ✅ WRONG PREFIX DETECTED - REDIRECT TO CORRECT URL
        
        // Replace wrong prefix with correct prefix
        $correctPath = preg_replace(
            '/^' . preg_quote($currentPrefix, '/') . '\//',
            $correctPrefix . '/',
            $currentPath
        );
        
        // Build full URL with query parameters
        $correctUrl = url($correctPath);
        if ($request->getQueryString()) {
            $correctUrl .= '?' . $request->getQueryString();
        }
        
        // Redirect to correct URL
        return redirect($correctUrl);
    }
}
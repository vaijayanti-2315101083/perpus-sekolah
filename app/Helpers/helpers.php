<?php

if (!function_exists('dashboard_route')) {
    /**
     * Get dashboard route based on user role
     *
     * @return string
     */
    function dashboard_route()
    {
        if (!auth()->check()) {
            return route('login');
        }

        $role = auth()->user()->role;

        return match ($role) {
            'Admin' => route('admin.dashboard'),
            'Pustakawan' => route('pustakawan.dashboard'),  // ✅ FIXED!
            'Member' => route('home'),
            default => route('home'),
        };
    }
}

if (!function_exists('profile_route')) {
    /**
     * Get profile route based on user role
     *
     * @return string
     */
    function profile_route()
    {
        if (!auth()->check()) {
            return route('login');
        }

        $role = auth()->user()->role;

        return match ($role) {
            'Admin' => route('admin.profile.index'),      // ✅ Admin profile
            'Pustakawan' => route('pustakawan.profile.index'),  // ✅ Pustakawan profile
            'Member' => route('profile.index'),            // ✅ Member profile
            default => route('profile.index'),
        };
    }
}

if (!function_exists('dynamic_route')) {
    /**
     * Get route with role-based prefix
     *
     * @param string $name Route name without prefix (e.g., 'books.index')
     * @param mixed $parameters
     * @param bool $absolute
     * @return string
     */
    function dynamic_route($name, $parameters = [], $absolute = true)
    {
        if (!auth()->check()) {
            return route('login');
        }

        $prefix = role_prefix();

        // Add prefix to route name
        $fullRouteName = $prefix ? "{$prefix}.{$name}" : $name;

        // Check if route exists
        if (\Illuminate\Support\Facades\Route::has($fullRouteName)) {
            return route($fullRouteName, $parameters, $absolute);
        }

        // Fallback to home if route doesn't exist
        return route('home');
    }
}

if (!function_exists('role_prefix')) {
    /**
     * Get URL prefix based on user role
     *
     * @return string
     */
    function role_prefix()
    {
        if (!auth()->check()) {
            return '';
        }

        $role = auth()->user()->role;

        return match ($role) {
            'Admin' => 'admin',
            'Pustakawan' => 'pustakawan',  // ✅ FIXED! Dulu 'admin' sekarang 'pustakawan'
            default => '',
        };
    }
}

if (!function_exists('user_can_access_route')) {
    /**
     * Check if user can access a specific route
     *
     * @param string $routeName
     * @return bool
     */
    function user_can_access_route($routeName)
    {
        if (!auth()->check()) {
            return false;
        }

        $role = auth()->user()->role;

        // Admin can access all routes
        if ($role === 'Admin') {
            return true;
        }

        // Pustakawan cannot access librarians routes
        if ($role === 'Pustakawan') {
            return !str_contains($routeName, 'librarians');
        }

        // Member can only access public and member routes
        if ($role === 'Member') {
            return !str_contains($routeName, 'admin') && !str_contains($routeName, 'pustakawan');
        }

        return false;
    }
}

if (!function_exists('get_role_home')) {
    /**
     * Get home route based on user role
     *
     * @return string
     */
    function get_role_home()
    {
        if (!auth()->check()) {
            return route('home');
        }

        $role = auth()->user()->role;

        return match ($role) {
            'Admin' => route('admin.dashboard'),
            'Pustakawan' => route('pustakawan.dashboard'),  // ✅ FIXED!
            'Member' => route('home'),
            default => route('home'),
        };
    }
}

if (!function_exists('is_current_role_route')) {
    /**
     * Check if current route matches the given route name pattern
     * Used for active menu highlighting in sidebar
     *
     * @param string $routePattern Route name pattern (e.g., 'dashboard', 'books.*', 'librarians.*')
     * @return bool
     */
    function is_current_role_route($routePattern)
    {
        if (!auth()->check()) {
            return false;
        }

        $currentRoute = request()->route()->getName();
        
        if (!$currentRoute) {
            return false;
        }

        // Get role prefix
        $prefix = role_prefix();
        
        // Build full route pattern with prefix
        $fullPattern = $prefix ? "{$prefix}.{$routePattern}" : $routePattern;
        
        // Check if it's a wildcard pattern (e.g., 'books.*')
        if (str_contains($fullPattern, '*')) {
            $pattern = str_replace('*', '.*', $fullPattern);
            return preg_match("/^{$pattern}$/", $currentRoute) === 1;
        }
        
        // Exact match
        return $currentRoute === $fullPattern;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupDynamicRouting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:dynamic-routing 
                            {--force : Overwrite existing files}
                            {--backup : Create backup of existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup dynamic routing system with role-based URL prefixes (admin, pustakawan, member)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Setting up Dynamic Routing System...');
        $this->newLine();

        $force = $this->option('force');
        $backup = $this->option('backup');

        // Create directories
        $this->createDirectories();

        // Generate files
        $this->generateHelpers($force, $backup);
        $this->generateDynamicRoutes($force, $backup);
        $this->generateMiddleware($force, $backup);
        $this->updateNavigationView($force, $backup);
        $this->updateSidebarView($force, $backup);
        $this->updateTopbarView($force, $backup);
        
        // Update config files
        $this->updateComposerJson();
        $this->updateKernel();
        $this->updateWebRoutes();

        $this->newLine();
        $this->info('✅ Dynamic Routing System setup completed!');
        $this->newLine();

        // Show next steps
        $this->displayNextSteps();

        return Command::SUCCESS;
    }

    /**
     * Create necessary directories
     */
    protected function createDirectories()
    {
        $directories = [
            app_path('Helpers'),
            app_path('Http/Middleware'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->line("📁 Created directory: {$directory}");
            }
        }
    }

    /**
     * Generate helpers file
     */
    protected function generateHelpers($force, $backup)
    {
        $helpersPath = app_path('Helpers/helpers.php');

        if (File::exists($helpersPath)) {
            if ($backup) {
                $backupPath = $helpersPath . '.backup';
                File::copy($helpersPath, $backupPath);
                $this->line("💾 Backup created: {$backupPath}");
            }

            if (!$force) {
                $this->warn('⚠️  helpers.php already exists. Use --force to overwrite.');
                return;
            }
        }

        $stub = $this->getHelpersStub();
        File::put($helpersPath, $stub);
        $this->info('✅ Helpers created: ' . $helpersPath);
    }

    /**
     * Generate dynamic routes file
     */
    protected function generateDynamicRoutes($force, $backup)
    {
        $routesPath = base_path('routes/dynamic_routes.php');

        if (File::exists($routesPath)) {
            if ($backup) {
                $backupPath = $routesPath . '.backup';
                File::copy($routesPath, $backupPath);
                $this->line("💾 Backup created: {$backupPath}");
            }

            if (!$force) {
                $this->warn('⚠️  dynamic_routes.php already exists. Use --force to overwrite.');
                return;
            }
        }

        $stub = $this->getDynamicRoutesStub();
        File::put($routesPath, $stub);
        $this->info('✅ Dynamic routes created: ' . $routesPath);
    }

    /**
     * Generate middleware
     */
    protected function generateMiddleware($force, $backup)
    {
        $middlewarePath = app_path('Http/Middleware/EnsureCorrectRolePrefix.php');

        if (File::exists($middlewarePath)) {
            if ($backup) {
                $backupPath = $middlewarePath . '.backup';
                File::copy($middlewarePath, $backupPath);
                $this->line("💾 Backup created: {$backupPath}");
            }

            if (!$force) {
                $this->warn('⚠️  EnsureCorrectRolePrefix middleware already exists. Use --force to overwrite.');
                return;
            }
        }

        $stub = $this->getMiddlewareStub();
        File::put($middlewarePath, $stub);
        $this->info('✅ Middleware created: ' . $middlewarePath);
    }

    /**
     * Update navigation view
     */
    protected function updateNavigationView($force, $backup)
    {
        $navigationPath = resource_path('views/layouts/navigation.blade.php');

        if (!File::exists($navigationPath)) {
            $this->warn('⚠️  navigation.blade.php not found. Skipping...');
            return;
        }

        if ($backup) {
            $backupPath = $navigationPath . '.backup';
            File::copy($navigationPath, $backupPath);
            $this->line("💾 Backup created: {$backupPath}");
        }

        if (!$force) {
            if (!$this->confirm('Update navigation.blade.php with dynamic routes?', true)) {
                return;
            }
        }

        $stub = $this->getNavigationStub();
        File::put($navigationPath, $stub);
        $this->info('✅ Navigation view updated: ' . $navigationPath);
    }

    /**
     * Update sidebar view
     */
    protected function updateSidebarView($force, $backup)
    {
        $sidebarPath = resource_path('views/layouts/sidebar.blade.php');

        if (!File::exists($sidebarPath)) {
            $this->warn('⚠️  sidebar.blade.php not found. Skipping...');
            return;
        }

        if ($backup) {
            $backupPath = $sidebarPath . '.backup';
            File::copy($sidebarPath, $backupPath);
            $this->line("💾 Backup created: {$backupPath}");
        }

        if (!$force) {
            if (!$this->confirm('Update sidebar.blade.php with dynamic routes?', true)) {
                return;
            }
        }

        $stub = $this->getSidebarStub();
        File::put($sidebarPath, $stub);
        $this->info('✅ Sidebar view updated: ' . $sidebarPath);
    }

    /**
     * Update topbar view
     */
    protected function updateTopbarView($force, $backup)
    {
        $topbarPath = resource_path('views/layouts/topbar.blade.php');

        if (!File::exists($topbarPath)) {
            $this->warn('⚠️  topbar.blade.php not found. Skipping...');
            return;
        }

        if ($backup) {
            $backupPath = $topbarPath . '.backup';
            File::copy($topbarPath, $backupPath);
            $this->line("💾 Backup created: {$backupPath}");
        }

        if (!$force) {
            if (!$this->confirm('Update topbar.blade.php with dynamic routes?', true)) {
                return;
            }
        }

        $stub = $this->getTopbarStub();
        File::put($topbarPath, $stub);
        $this->info('✅ Topbar view updated: ' . $topbarPath);
    }

    /**
     * Update composer.json
     */
    protected function updateComposerJson()
    {
        $composerPath = base_path('composer.json');

        if (!File::exists($composerPath)) {
            $this->error('❌ composer.json not found!');
            return;
        }

        $composer = json_decode(File::get($composerPath), true);

        // Check if helpers already in autoload
        if (isset($composer['autoload']['files']) && in_array('app/Helpers/helpers.php', $composer['autoload']['files'])) {
            $this->line('ℹ️  Helpers already in composer.json autoload');
            return;
        }

        // Add helpers to autoload files
        if (!isset($composer['autoload']['files'])) {
            $composer['autoload']['files'] = [];
        }

        $composer['autoload']['files'][] = 'app/Helpers/helpers.php';

        File::put($composerPath, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info('✅ composer.json updated with helpers autoload');
        
        $this->line('🔄 Running: composer dump-autoload');
        exec('composer dump-autoload');
    }

    /**
     * Update Kernel.php
     */
    protected function updateKernel()
    {
        $kernelPath = app_path('Http/Kernel.php');

        if (!File::exists($kernelPath)) {
            $this->error('❌ Kernel.php not found!');
            return;
        }

        $content = File::get($kernelPath);

        // Check if middleware already registered
        if (strpos($content, "'role.prefix'") !== false) {
            $this->line('ℹ️  Middleware already registered in Kernel.php');
            return;
        }

        // Add middleware to routeMiddleware array
        $pattern = "/(protected \\\$routeMiddleware = \[.*?)'verified'/s";
        $replacement = "$1'verified' => \\Illuminate\\Auth\\Middleware\\EnsureEmailIsVerified::class,\n        'role.prefix' => \\App\\Http\\Middleware\\EnsureCorrectRolePrefix::class";

        $content = preg_replace($pattern, $replacement, $content);

        File::put($kernelPath, $content);
        $this->info('✅ Kernel.php updated with middleware registration');
    }

    /**
     * Update routes/web.php
     */
    protected function updateWebRoutes()
    {
        $webRoutesPath = base_path('routes/web.php');

        if (!File::exists($webRoutesPath)) {
            $this->error('❌ routes/web.php not found!');
            return;
        }

        $content = File::get($webRoutesPath);

        // Check if dynamic routes already included
        if (strpos($content, "require __DIR__.'/dynamic_routes.php'") !== false) {
            $this->line('ℹ️  Dynamic routes already included in web.php');
            return;
        }

        // Add require at the end
        $content .= "\n// Dynamic Admin Routes (Role-based prefix)\n";
        $content .= "require __DIR__.'/dynamic_routes.php';\n";

        File::put($webRoutesPath, $content);
        $this->info('✅ routes/web.php updated with dynamic routes');
    }

    /**
     * Display next steps
     */
    protected function displayNextSteps()
    {
        $this->line('📋 <fg=yellow>NEXT STEPS:</>');
        $this->newLine();

        $this->line('1️⃣  Clear caches:');
        $this->line('   <fg=green>php artisan route:clear</>');
        $this->line('   <fg=green>php artisan config:clear</>');
        $this->line('   <fg=green>php artisan cache:clear</>');
        $this->newLine();

        $this->line('2️⃣  Test the routing:');
        $this->line('   <fg=cyan>Admin: Login and access /admin/dashboard</>');
        $this->line('   <fg=cyan>Pustakawan: Login and access /pustakawan/dashboard</>');
        $this->line('   <fg=cyan>Member: Login and access /profile</>');
        $this->newLine();

        $this->line('3️⃣  Verify routes:');
        $this->line('   <fg=green>php artisan route:list | grep admin</>');
        $this->line('   <fg=green>php artisan route:list | grep pustakawan</>');
        $this->newLine();

        $this->line('4️⃣  <fg=red>IMPORTANT: Comment out old static admin routes in web.php</>');
        $this->line('   Old routes with single /admin prefix should be disabled');
        $this->newLine();

        $this->line('✨ <fg=green>URL Structure:</>');
        $this->line('   Admin:      <fg=cyan>/admin/dashboard, /admin/profile, /admin/books</>');
        $this->line('   Pustakawan: <fg=cyan>/pustakawan/dashboard, /pustakawan/profile, /pustakawan/books</>');
        $this->line('   Member:     <fg=cyan>/profile, /my-books</> (clean URLs)');
        $this->newLine();

        $this->line('📚 Full documentation created at: <fg=cyan>storage/logs/dynamic_routing_setup.log</>');
    }

    /**
     * Get helpers stub
     */
    protected function getHelpersStub()
    {
        return <<<'PHP'
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
            return route('home');
        }

        $role = auth()->user()->role;

        return match ($role) {
            'Admin' => route('admin.dashboard'),
            'Pustakawan' => route('pustakawan.dashboard'),
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
            'Admin' => route('admin.profile.index'),
            'Pustakawan' => route('pustakawan.profile.index'),
            'Member' => route('profile.index'),
            default => route('profile.index'),
        };
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
            'Pustakawan' => 'pustakawan',
            'Member' => '',
            default => '',
        };
    }
}

if (!function_exists('role_route_name')) {
    /**
     * Get route name prefix based on user role
     * 
     * @return string
     */
    function role_route_name()
    {
        if (!auth()->check()) {
            return '';
        }

        $role = auth()->user()->role;

        return match ($role) {
            'Admin' => 'admin.',
            'Pustakawan' => 'pustakawan.',
            'Member' => '',
            default => '',
        };
    }
}

if (!function_exists('dynamic_route')) {
    /**
     * Generate dynamic route based on user role
     * 
     * @param string $routeName Route name without prefix (e.g., 'dashboard', 'books.index')
     * @param array $parameters Route parameters
     * @return string
     */
    function dynamic_route($routeName, $parameters = [])
    {
        if (!auth()->check()) {
            return route('home');
        }

        $prefix = role_route_name();
        $fullRouteName = $prefix . $routeName;

        // Check if route exists
        if (\Illuminate\Support\Facades\Route::has($fullRouteName)) {
            return route($fullRouteName, $parameters);
        }

        // Fallback
        return route('home');
    }
}

if (!function_exists('is_current_role_route')) {
    /**
     * Check if current route matches user role prefix
     * 
     * @param string $pattern Route pattern
     * @return bool
     */
    function is_current_role_route($pattern)
    {
        $prefix = role_route_name();
        return request()->routeIs($prefix . $pattern);
    }
}
PHP;
    }

    /**
     * Get dynamic routes stub
     */
    protected function getDynamicRoutesStub()
    {
        return <<<'PHP'
<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowController;
use App\Http\Controllers\Admin\LibrarianController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RestoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dynamic Admin Routes (Multi-Prefix)
|--------------------------------------------------------------------------
*/

// Function to register routes with different prefixes
$registerAdminRoutes = function ($prefix, $name) {
    Route::middleware(['auth', 'superuser'])->prefix($prefix)->name($name)->group(function () {
        
        // Dashboard
        Route::get('/dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Profile Management
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/profile', 'index')->name('profile.index');
            Route::put('/profile', 'update')->name('profile.update');
            Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
            Route::put('/profile/photo', 'updatePhoto')->name('profile.photo.update');
            Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
        });

        // Books Management
        Route::resource('books', BookController::class)->except(['show']);

        // Members Management
        Route::resource('members', MemberController::class)->except(['show']);

        // Borrows Management
        Route::resource('borrows', BorrowController::class)->except(['show', 'create', 'store']);

        // Returns Management
        Route::controller(RestoreController::class)->group(function () {
            Route::get('/returns', 'index')->name('returns.index');
            Route::get('/returns/{restore}/edit', 'edit')->name('returns.edit');
            Route::put('/returns/{restore}', 'update')->name('returns.update');
            Route::delete('/returns/{restore}', 'destroy')->name('returns.destroy');
        });
    });
};

// Register routes for Admin with /admin prefix
$registerAdminRoutes('admin', 'admin.');

// Register routes for Pustakawan with /pustakawan prefix
$registerAdminRoutes('pustakawan', 'pustakawan.');

// Admin Only Routes (Librarian Management)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('librarians', LibrarianController::class)->except(['show']);
});
PHP;
    }

    /**
     * Get middleware stub
     */
    protected function getMiddlewareStub()
    {
        return <<<'PHP'
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
        if (!auth()->check()) {
            return $next($request);
        }

        $user = auth()->user();
        $currentPath = $request->path();

        // Determine correct prefix for user role
        $correctPrefix = match ($user->role) {
            'Admin' => 'admin',
            'Pustakawan' => 'pustakawan',
            default => null,
        };

        // If user is Member, no prefix check needed
        if (!$correctPrefix) {
            return $next($request);
        }

        // Check if current path starts with admin or pustakawan
        if (preg_match('/^(admin|pustakawan)\//', $currentPath, $matches)) {
            $currentPrefix = $matches[1];

            // If prefix doesn't match user role, redirect to correct prefix
            if ($currentPrefix !== $correctPrefix) {
                $newPath = preg_replace('/^(admin|pustakawan)\//', $correctPrefix . '/', $currentPath);
                return redirect($newPath);
            }
        }

        return $next($request);
    }
}
PHP;
    }

    /**
     * Get navigation stub
     */
    protected function getNavigationStub()
    {
        return <<<'BLADE'
<div class="fixed-top">
    @auth
        @if (in_array(auth()->user()->role, [\App\Models\User::ROLES['Admin'], \App\Models\User::ROLES['Librarian']]))
            <div class="navbar px-5 bg-primary-subtle flex justify-content-between">
                <span>Anda adalah <b>{{ auth()->user()->role }}</b></span>

                <a href="{{ dashboard_route() }}" class="btn btn-primary">Ke Dashboard</a>
            </div>
        @endif
    @endauth

    <nav class="navbar navbar-expand-lg bg-body-tertiary px-3">
        <div class="container-fluid">
            <a class="navbar-brand fs-4 fw-bold" href="{{ route('home') }}">Perpustakaan</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarItems"
                aria-controls="navbarItems" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarItems">
                <div class="navbar-nav ms-auto">
                    @auth
                        <a class="nav-link {{ request()->routeIs('my-books.*') ? 'active' : '' }}" href="{{ route('my-books.index') }}">Buku-ku</a>

                        <a class="nav-link {{ request()->routeIs('profile.*') || is_current_role_route('profile.*') ? 'active' : '' }}" 
                           href="{{ profile_route() }}">Profil</a>

                        <form action="{{ route('logout') }}" method="POST"
                            onsubmit="return confirm('Anda yakin ingin keluar?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-link nav-link" type="submit">Logout</button>
                        </form>
                    @endauth

                    @guest
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    @endguest
                </div>
            </div>
        </div>
    </nav>
</div>
BLADE;
    }

    /**
     * Get sidebar stub
     */
    protected function getSidebarStub()
    {
        return <<<'BLADE'
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-text mx-3">Perpustakaan</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Dashboard -->
    <li class="nav-item {{ is_current_role_route('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dashboard_route() }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    @if (auth()->user()->role === \App\Models\User::ROLES['Admin'])
        <!-- Pustakawan (Admin Only) -->
        <li class="nav-item {{ is_current_role_route('librarians.*') ? 'active' : '' }}">
            <a class="nav-link" href="{{ dynamic_route('librarians.index') }}">
                <i class="fas fa-fw fa-user-tie"></i>
                <span>Pustakawan</span>
            </a>
        </li>
    @endif

    <!-- Members -->
    <li class="nav-item {{ is_current_role_route('members.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dynamic_route('members.index') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>Member</span>
        </a>
    </li>

    <!-- Books -->
    <li class="nav-item {{ is_current_role_route('books.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dynamic_route('books.index') }}">
            <i class="fas fa-fw fa-book"></i>
            <span>Buku</span>
        </a>
    </li>

    <!-- Borrows -->
    <li class="nav-item {{ is_current_role_route('borrows.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dynamic_route('borrows.index') }}">
            <i class="fas fa-fw fa-copy"></i>
            <span>Peminjaman</span>
        </a>
    </li>

    <!-- Returns -->
    <li class="nav-item {{ is_current_role_route('returns.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ dynamic_route('returns.index') }}">
            <i class="fas fa-fw fa-paste"></i>
            <span>Pengembalian</span>
        </a>
    </li>

    <!-- Sidebar Toggler -->
    <div class="mt-5 text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
BLADE;
    }

    /**
     * Get topbar stub
     */
    protected function getTopbarStub()
    {
        return <<<'BLADE'
<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-inline text-gray-600 small">{{ auth()->user()->name }}</span>
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{ profile_route() }}">
                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                    Profil
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

</nav>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="logoutModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Anda yakin ingin keluar?</div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    @method('DELETE')

                    <button class="btn btn-primary" type="submit">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>
BLADE;
    }
}

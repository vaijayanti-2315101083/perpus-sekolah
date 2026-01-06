<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupRoleDashboardProtection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:role-dashboard 
                            {--force : Overwrite existing files}
                            {--backup : Create backup of existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup separate dashboards for Admin/Pustakawan and add strict role protection';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Setting up Role-Based Dashboard & Protection...');
        $this->newLine();

        $force = $this->option('force');
        $backup = $this->option('backup');

        // Generate files
        $this->generateDashboardController($force, $backup);
        $this->generateAdminDashboardView($force, $backup);
        $this->generatePustakawanDashboardView($force, $backup);
        $this->generateStrictRoleMiddleware($force, $backup);
        $this->updateDynamicRoutes($force, $backup);
        $this->updateKernel();

        $this->newLine();
        $this->info('✅ Role-Based Dashboard & Protection setup completed!');
        $this->newLine();

        // Show next steps
        $this->displayNextSteps();

        return Command::SUCCESS;
    }

    /**
     * Generate DashboardController
     */
    protected function generateDashboardController($force, $backup)
    {
        $controllerPath = app_path('Http/Controllers/Admin/DashboardController.php');

        if (File::exists($controllerPath)) {
            if ($backup) {
                $backupPath = $controllerPath . '.backup';
                File::copy($controllerPath, $backupPath);
                $this->line("💾 Backup created: {$backupPath}");
            }

            if (!$force) {
                if (!$this->confirm('DashboardController already exists. Overwrite?', true)) {
                    return;
                }
            }
        }

        $stub = $this->getDashboardControllerStub();
        File::put($controllerPath, $stub);
        $this->info('✅ DashboardController created: ' . $controllerPath);
    }

    /**
     * Generate Admin Dashboard View
     */
    protected function generateAdminDashboardView($force, $backup)
    {
        $viewPath = resource_path('views/admin/dashboard-admin.blade.php');

        if (File::exists($viewPath)) {
            if ($backup) {
                $backupPath = $viewPath . '.backup';
                File::copy($viewPath, $backupPath);
                $this->line("💾 Backup created: {$backupPath}");
            }

            if (!$force) {
                if (!$this->confirm('Admin dashboard view already exists. Overwrite?', true)) {
                    return;
                }
            }
        }

        $stub = $this->getAdminDashboardViewStub();
        File::put($viewPath, $stub);
        $this->info('✅ Admin dashboard view created: ' . $viewPath);
    }

    /**
     * Generate Pustakawan Dashboard View
     */
    protected function generatePustakawanDashboardView($force, $backup)
    {
        $viewPath = resource_path('views/admin/dashboard-pustakawan.blade.php');

        if (File::exists($viewPath)) {
            if ($backup) {
                $backupPath = $viewPath . '.backup';
                File::copy($viewPath, $backupPath);
                $this->line("💾 Backup created: {$backupPath}");
            }

            if (!$force) {
                if (!$this->confirm('Pustakawan dashboard view already exists. Overwrite?', true)) {
                    return;
                }
            }
        }

        $stub = $this->getPustakawanDashboardViewStub();
        File::put($viewPath, $stub);
        $this->info('✅ Pustakawan dashboard view created: ' . $viewPath);
    }

    /**
     * Generate Strict Role Middleware
     */
    protected function generateStrictRoleMiddleware($force, $backup)
    {
        $middlewarePath = app_path('Http/Middleware/StrictRoleAccess.php');

        if (File::exists($middlewarePath)) {
            if ($backup) {
                $backupPath = $middlewarePath . '.backup';
                File::copy($middlewarePath, $backupPath);
                $this->line("💾 Backup created: {$backupPath}");
            }

            if (!$force) {
                if (!$this->confirm('StrictRoleAccess middleware already exists. Overwrite?', true)) {
                    return;
                }
            }
        }

        $stub = $this->getStrictRoleMiddlewareStub();
        File::put($middlewarePath, $stub);
        $this->info('✅ StrictRoleAccess middleware created: ' . $middlewarePath);
    }

    /**
     * Update dynamic routes
     */
    protected function updateDynamicRoutes($force, $backup)
    {
        $routesPath = base_path('routes/dynamic_routes.php');

        if (!File::exists($routesPath)) {
            $this->warn('⚠️  dynamic_routes.php not found. Creating new one...');
            $stub = $this->getDynamicRoutesStub();
            File::put($routesPath, $stub);
            $this->info('✅ dynamic_routes.php created');
            return;
        }

        if ($backup) {
            $backupPath = $routesPath . '.backup';
            File::copy($routesPath, $backupPath);
            $this->line("💾 Backup created: {$backupPath}");
        }

        if (!$force) {
            if (!$this->confirm('Update dynamic_routes.php with DashboardController?', true)) {
                return;
            }
        }

        $content = File::get($routesPath);

        // Replace dashboard closure with controller
        $pattern = "/Route::get\('\/dashboard', function \(\) \{.*?\}\)->name\('dashboard'\);/s";
        $replacement = "Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');";

        $content = preg_replace($pattern, $replacement, $content);

        // Add use statement if not exists
        if (strpos($content, 'use App\Http\Controllers\Admin\DashboardController;') === false) {
            $content = preg_replace(
                '/(use App\\\\Http\\\\Controllers\\\\Admin\\\\BookController;)/',
                "use App\\Http\\Controllers\\Admin\\DashboardController;\n$1",
                $content
            );
        }

        File::put($routesPath, $content);
        $this->info('✅ dynamic_routes.php updated with DashboardController');
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
        if (strpos($content, "'strict.role'") !== false) {
            $this->line('ℹ️  StrictRoleAccess middleware already registered in Kernel.php');
            return;
        }

        // Add middleware after role.prefix
        $pattern = "/'role\.prefix' => \\\\App\\\\Http\\\\Middleware\\\\EnsureCorrectRolePrefix::class,/";
        $replacement = "'role.prefix' => \\App\\Http\\Middleware\\EnsureCorrectRolePrefix::class,\n        'strict.role' => \\App\\Http\\Middleware\\StrictRoleAccess::class,";

        $content = preg_replace($pattern, $replacement, $content);

        File::put($kernelPath, $content);
        $this->info('✅ Kernel.php updated with StrictRoleAccess middleware');
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
        $this->line('   <fg=green>php artisan view:clear</>');
        $this->newLine();

        $this->line('2️⃣  Test Admin Dashboard:');
        $this->line('   <fg=cyan>Login as Admin → Visit /admin/dashboard</>');
        $this->line('   <fg=cyan>Should see: Full statistics (users, books, borrows, returns)</>');
        $this->newLine();

        $this->line('3️⃣  Test Pustakawan Dashboard:');
        $this->line('   <fg=cyan>Login as Pustakawan → Visit /pustakawan/dashboard</>');
        $this->line('   <fg=cyan>Should see: Operational stats (pending confirmations, tasks)</>');
        $this->newLine();

        $this->line('4️⃣  Test Strict Protection:');
        $this->line('   <fg=red>Pustakawan tries to access /admin/librarians</>');
        $this->line('   <fg=green>→ Should redirect to /pustakawan/dashboard with error message</>');
        $this->newLine();

        $this->line('✨ <fg=green>Features Added:</>');
        $this->line('   ✅ Separate dashboard for Admin (full control view)');
        $this->line('   ✅ Separate dashboard for Pustakawan (operational view)');
        $this->line('   ✅ Strict role protection (Pustakawan cannot access Admin routes)');
        $this->line('   ✅ Automatic redirect with error message');
        $this->newLine();
    }

    /**
     * Get DashboardController stub
     */
    protected function getDashboardControllerStub()
    {
        return <<<'PHP'
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrow;
use App\Models\Restore;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        // Admin Dashboard
        if ($user->role === User::ROLES['Admin']) {
            return $this->adminDashboard();
        }

        // Pustakawan Dashboard
        if ($user->role === User::ROLES['Librarian']) {
            return $this->pustakawanDashboard();
        }

        // Member Dashboard (redirect to home)
        return redirect()->route('home');
    }

    /**
     * Admin Dashboard - Full Control View
     */
    protected function adminDashboard()
    {
        $data = [
            // Books Statistics
            'total_books' => Book::count(),
            'available_books' => Book::where('status', Book::STATUSES['Available'])->count(),
            'unavailable_books' => Book::where('status', Book::STATUSES['Unavailable'])->count(),

            // User Statistics (Admin can see all)
            'total_users' => User::count(),
            'total_admins' => User::where('role', User::ROLES['Admin'])->count(),
            'total_librarians' => User::where('role', User::ROLES['Librarian'])->count(),
            'total_members' => User::where('role', User::ROLES['Member'])->count(),

            // Borrow Statistics
            'pending_borrows' => Borrow::where('confirmation', false)->count(),
            'confirmed_borrows' => Borrow::where('confirmation', true)->count(),
            'total_borrows' => Borrow::count(),

            // Return Statistics
            'pending_returns' => Restore::where('status', Restore::STATUSES['Not confirmed'])->count(),
            'overdue_returns' => Restore::where('status', Restore::STATUSES['Past due'])->count(),
            'unpaid_fines' => Restore::where('status', Restore::STATUSES['Fine not paid'])->count(),
            'completed_returns' => Restore::where('status', Restore::STATUSES['Returned'])->count(),

            // Recent Activities
            'recent_borrows' => Borrow::with(['user', 'book'])
                ->latest('borrowed_at')
                ->take(5)
                ->get(),
            
            'recent_returns' => Restore::with(['user', 'book'])
                ->latest('returned_at')
                ->take(5)
                ->get(),

            // Books that need attention
            'low_stock_books' => Book::where('amount', '<=', 2)
                ->where('amount', '>', 0)
                ->take(5)
                ->get(),
            
            'out_of_stock_books' => Book::where('amount', 0)->count(),
        ];

        return view('admin.dashboard-admin', $data);
    }

    /**
     * Pustakawan Dashboard - Operational View
     */
    protected function pustakawanDashboard()
    {
        $data = [
            // Books Statistics (operational focus)
            'total_books' => Book::count(),
            'available_books' => Book::where('status', Book::STATUSES['Available'])->count(),
            'low_stock_books_count' => Book::where('amount', '<=', 2)->where('amount', '>', 0)->count(),

            // Member Statistics only (cannot see admin/librarian)
            'total_members' => User::where('role', User::ROLES['Member'])->count(),

            // Pending Tasks (what needs action NOW)
            'pending_borrow_confirmations' => Borrow::where('confirmation', false)->count(),
            'pending_return_confirmations' => Restore::where('status', Restore::STATUSES['Not confirmed'])->count(),
            'overdue_to_process' => Restore::where('status', Restore::STATUSES['Past due'])->count(),
            'fines_to_collect' => Restore::where('status', Restore::STATUSES['Fine not paid'])->count(),

            // Today's Activity
            'borrows_today' => Borrow::whereDate('borrowed_at', today())->count(),
            'returns_today' => Restore::whereDate('returned_at', today())->count(),

            // Active Operations
            'active_borrows' => Borrow::where('confirmation', true)
                ->whereDoesntHave('restore')
                ->count(),

            // Recent Pending Actions (tasks to handle)
            'recent_pending_borrows' => Borrow::with(['user', 'book'])
                ->where('confirmation', false)
                ->latest('borrowed_at')
                ->take(5)
                ->get(),
            
            'recent_pending_returns' => Restore::with(['user', 'book'])
                ->whereIn('status', [
                    Restore::STATUSES['Not confirmed'],
                    Restore::STATUSES['Past due'],
                    Restore::STATUSES['Fine not paid']
                ])
                ->latest('returned_at')
                ->take(5)
                ->get(),

            // Books that need restocking
            'low_stock_books' => Book::where('amount', '<=', 2)
                ->where('amount', '>', 0)
                ->orderBy('amount', 'asc')
                ->take(5)
                ->get(),
        ];

        return view('admin.dashboard-pustakawan', $data);
    }
}
PHP;
    }

    /**
     * Get Admin Dashboard View stub
     */
    protected function getAdminDashboardViewStub()
    {
        return <<<'BLADE'
<x-admin-layout title="Dashboard Admin">
    {{-- Welcome Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Selamat Datang, Administrator
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-muted small mt-1">
                                Anda memiliki akses penuh ke seluruh sistem perpustakaan.
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards Row 1: Books --}}
    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Buku
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_books }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Buku Tersedia
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $available_books }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Stok Menipis
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $low_stock_books->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Buku Habis
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $out_of_stock_books }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards Row 2: Users --}}
    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Pengguna
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_users }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Admin
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_admins }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Pustakawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_librarians }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Member
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_members }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards Row 3: Borrows & Returns --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Peminjaman Pending
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $pending_borrows }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Peminjaman
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_borrows }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Pengembalian Terlambat
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overdue_returns }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Denda Belum Dibayar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $unpaid_fines }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions for Admin --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat Admin</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('librarians.index') }}" class="btn btn-outline-primary btn-block py-3">
                                <i class="fas fa-user-tie fa-2x d-block mb-2"></i>
                                <strong>Kelola Pustakawan</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('members.index') }}" class="btn btn-outline-success btn-block py-3">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                <strong>Kelola Member</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('books.index') }}" class="btn btn-outline-info btn-block py-3">
                                <i class="fas fa-book fa-2x d-block mb-2"></i>
                                <strong>Kelola Buku</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('borrows.index') }}" class="btn btn-outline-warning btn-block py-3">
                                <i class="fas fa-clipboard-list fa-2x d-block mb-2"></i>
                                <strong>Kelola Peminjaman</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Peminjaman Terbaru</h6>
                </div>
                <div class="card-body">
                    @if($recent_borrows->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Peminjam</th>
                                        <th>Buku</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_borrows as $borrow)
                                        <tr>
                                            <td>{{ $borrow->user->name }}</td>
                                            <td>{{ Str::limit($borrow->book->title, 25) }}</td>
                                            <td>
                                                @if($borrow->confirmation)
                                                    <span class="badge badge-success">Dikonfirmasi</span>
                                                @else
                                                    <span class="badge badge-warning">Pending</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Belum ada peminjaman</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Buku Stok Menipis</h6>
                </div>
                <div class="card-body">
                    @if($low_stock_books->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Judul Buku</th>
                                        <th>Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($low_stock_books as $book)
                                        <tr>
                                            <td>{{ Str::limit($book->title, 30) }}</td>
                                            <td>
                                                <span class="badge badge-warning">{{ $book->amount }} buku</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Semua buku stok aman</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
BLADE;
    }

    /**
     * Get Pustakawan Dashboard View stub
     */
    protected function getPustakawanDashboardViewStub()
    {
        return <<<'BLADE'
<x-admin-layout title="Dashboard Pustakawan">
    {{-- Welcome Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Selamat Datang, Pustakawan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="text-muted small mt-1">
                                Kelola operasional perpustakaan: konfirmasi peminjaman dan pengembalian buku.
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-3x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pending Tasks (Priority Actions) --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-warning shadow">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-tasks mr-2"></i>Tugas yang Perlu Dikerjakan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('borrows.index') }}" class="text-decoration-none">
                                <div class="card border-left-warning h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                    Peminjaman Pending
                                                </div>
                                                <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                    {{ $pending_borrow_confirmations }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-clipboard-check fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('returns.index') }}" class="text-decoration-none">
                                <div class="card border-left-info h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                    Pengembalian Pending
                                                </div>
                                                <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                    {{ $pending_return_confirmations }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-undo fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('returns.index') }}" class="text-decoration-none">
                                <div class="card border-left-danger h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                    Terlambat (Proses Denda)
                                                </div>
                                                <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                    {{ $overdue_to_process }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('returns.index') }}" class="text-decoration-none">
                                <div class="card border-left-dark h-100">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                                    Denda Belum Dibayar
                                                </div>
                                                <div class="h3 mb-0 font-weight-bold text-gray-800">
                                                    {{ $fines_to_collect }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-3">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Buku
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_books }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Buku Tersedia
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $available_books }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Member
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $total_members }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Peminjaman Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $active_borrows }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Today's Activity --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Peminjaman Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $borrows_today }} peminjaman
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pengembalian Hari Ini
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $returns_today }} pengembalian
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('members.index') }}" class="btn btn-outline-success btn-block py-3">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                <strong>Kelola Member</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('books.index') }}" class="btn btn-outline-info btn-block py-3">
                                <i class="fas fa-book fa-2x d-block mb-2"></i>
                                <strong>Kelola Buku</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('borrows.index') }}" class="btn btn-outline-warning btn-block py-3">
                                <i class="fas fa-clipboard-list fa-2x d-block mb-2"></i>
                                <strong>Kelola Peminjaman</strong>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ dynamic_route('returns.index') }}" class="btn btn-outline-danger btn-block py-3">
                                <i class="fas fa-undo fa-2x d-block mb-2"></i>
                                <strong>Kelola Pengembalian</strong>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Pending Activities --}}
    <div class="row">
        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">Peminjaman Perlu Dikonfirmasi</h6>
                </div>
                <div class="card-body">
                    @if($recent_pending_borrows->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Peminjam</th>
                                        <th>Buku</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_pending_borrows as $borrow)
                                        <tr>
                                            <td>{{ $borrow->user->name }}</td>
                                            <td>{{ Str::limit($borrow->book->title, 20) }}</td>
                                            <td>
                                                <a href="{{ dynamic_route('borrows.edit', $borrow) }}" class="btn btn-sm btn-warning">
                                                    Konfirmasi
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Tidak ada peminjaman pending</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-info">
                    <h6 class="m-0 font-weight-bold text-white">Pengembalian Perlu Dikonfirmasi</h6>
                </div>
                <div class="card-body">
                    @if($recent_pending_returns->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Peminjam</th>
                                        <th>Buku</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recent_pending_returns as $return)
                                        <tr>
                                            <td>{{ $return->user->name }}</td>
                                            <td>{{ Str::limit($return->book->title, 20) }}</td>
                                            <td>
                                                <a href="{{ dynamic_route('returns.edit', $return) }}" class="btn btn-sm btn-info">
                                                    Proses
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-muted py-3">Tidak ada pengembalian pending</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Books Need Restocking --}}
    @if($low_stock_books->count() > 0)
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card border-left-warning shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Buku Perlu Restocking
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Judul Buku</th>
                                        <th>Penulis</th>
                                        <th>Sisa Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($low_stock_books as $book)
                                        <tr>
                                            <td>{{ $book->title }}</td>
                                            <td>{{ $book->writer }}</td>
                                            <td>
                                                <span class="badge badge-warning">{{ $book->amount }} buku</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-admin-layout>
BLADE;
    }

    /**
     * Get Strict Role Middleware stub
     */
    protected function getStrictRoleMiddlewareStub()
    {
        return <<<'PHP'
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
PHP;
    }

    /**
     * Get Dynamic Routes stub
     */
    protected function getDynamicRoutesStub()
    {
        return <<<'PHP'
<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LibrarianController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RestoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dynamic Admin Routes (Multi-Prefix with Strict Protection)
|--------------------------------------------------------------------------
*/

// Function to register routes with different prefixes
$registerAdminRoutes = function ($prefix, $name) {
    Route::middleware(['auth', 'superuser', 'strict.role'])->prefix($prefix)->name($name)->group(function () {
        
        // Dashboard (uses DashboardController)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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

// Admin Only Routes (Librarian Management) - PROTECTED with strict.role
Route::middleware(['auth', 'admin', 'strict.role'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('librarians', LibrarianController::class)->except(['show']);
});
PHP;
    }
}
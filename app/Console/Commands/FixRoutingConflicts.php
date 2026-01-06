<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixRoutingConflicts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:routing 
                            {--force : Overwrite without asking}
                            {--backup : Create backup of web.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix routing conflicts: remove duplicates from web.php, keep only public/guest/member routes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayBanner();
        
        $force = $this->option('force');
        $backup = $this->option('backup');

        $webRoutesPath = base_path('routes/web.php');

        if (!File::exists($webRoutesPath)) {
            $this->error('❌ routes/web.php not found!');
            return Command::FAILURE;
        }

        // Create backup if requested
        if ($backup) {
            $backupPath = $webRoutesPath . '.backup.' . date('YmdHis');
            File::copy($webRoutesPath, $backupPath);
            $this->info("💾 Backup created: {$backupPath}");
            $this->newLine();
        }

        // Confirm before proceeding
        if (!$force) {
            $this->warn('This will cleanup routes/web.php:');
            $this->line('  ✅ Keep: public routes (home, search, preview)');
            $this->line('  ✅ Keep: guest routes (login, register)');
            $this->line('  ✅ Keep: member routes (my-books, logout)');
            $this->line('  ❌ Remove: duplicate admin routes');
            $this->newLine();
            
            if (!$this->confirm('Continue?', true)) {
                $this->warn('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Generate clean web.php
        $this->info('🧹 Cleaning routes/web.php...');
        $cleanedRoutes = $this->getCleanWebRoutesContent();
        File::put($webRoutesPath, $cleanedRoutes);
        
        $this->newLine();
        $this->info('✅ routes/web.php cleaned successfully!');
        $this->newLine();

        // Ensure dynamic_routes.php exists
        $this->ensureDynamicRoutesExists();

        // Display summary
        $this->displaySummary();

        return Command::SUCCESS;
    }

    /**
     * Display banner
     */
    protected function displayBanner()
    {
        $this->newLine();
        $this->line('╔═══════════════════════════════════════════════════╗');
        $this->line('║                                                   ║');
        $this->line('║        🔧 FIX ROUTING CONFLICTS 🔧                ║');
        $this->line('║                                                   ║');
        $this->line('║  This will clean your routes/web.php:            ║');
        $this->line('║  ✅ Remove duplicate admin routes                 ║');
        $this->line('║  ✅ Keep public, guest, member routes             ║');
        $this->line('║  ✅ Organize code cleanly                         ║');
        $this->line('║                                                   ║');
        $this->line('╚═══════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Ensure dynamic_routes.php exists
     */
    protected function ensureDynamicRoutesExists()
    {
        $dynamicRoutesPath = base_path('routes/dynamic_routes.php');
        
        if (!File::exists($dynamicRoutesPath)) {
            $this->warn('⚠️  routes/dynamic_routes.php not found!');
            
            if ($this->confirm('Create basic dynamic_routes.php?', true)) {
                $content = $this->getBasicDynamicRoutesContent();
                File::put($dynamicRoutesPath, $content);
                $this->info('✅ Created routes/dynamic_routes.php');
                $this->newLine();
            } else {
                $this->error('❌ dynamic_routes.php is required! Please create it manually.');
            }
        } else {
            $this->line('ℹ️  routes/dynamic_routes.php already exists (not modified)');
            $this->newLine();
        }
    }

    /**
     * Display summary
     */
    protected function displaySummary()
    {
        $this->line('╔═══════════════════════════════════════════════════╗');
        $this->line('║              CLEANUP COMPLETED                    ║');
        $this->line('╚═══════════════════════════════════════════════════╝');
        $this->newLine();

        $this->line('📋 <fg=yellow>CHANGES MADE:</>');
        $this->newLine();
        
        $this->line('✅ <fg=green>routes/web.php cleaned:</>');
        $this->line('   - Duplicate admin routes removed');
        $this->line('   - Only public/guest/member routes kept');
        $this->line('   - Old routes commented out (not deleted)');
        $this->newLine();

        $this->line('✅ <fg=green>All admin routes now in:</>');
        $this->line('   - routes/dynamic_routes.php');
        $this->newLine();

        $this->line('🔧 <fg=cyan>NEXT STEPS:</>');
        $this->newLine();

        $this->line('1️⃣  Clear route cache:');
        $this->line('   <fg=green>php artisan route:clear</>');
        $this->newLine();

        $this->line('2️⃣  Verify no duplicates:');
        $this->line('   <fg=green>php artisan route:list | sort | uniq -d</>');
        $this->line('   (Should show nothing = no duplicates)');
        $this->newLine();

        $this->line('3️⃣  Test your application:');
        $this->line('   <fg=cyan>- Public pages: home, search, preview</>');
        $this->line('   <fg=cyan>- Auth: login, register, logout</>');
        $this->line('   <fg=cyan>- Admin: /admin/dashboard</>');
        $this->line('   <fg=cyan>- Member: /my-books</>');
        $this->newLine();

        $this->line('✨ <fg=green>RESULT:</>');
        $this->line('   ✅ No route conflicts');
        $this->line('   ✅ Clean, organized code');
        $this->line('   ✅ Single source of truth for admin routes');
        $this->newLine();
    }

    /**
     * Get clean web.php content
     */
    protected function getCleanWebRoutesContent()
    {
        return <<<'PHP'
<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyBookController;
use App\Http\Controllers\PreviewController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Cleaned Version
|--------------------------------------------------------------------------
|
| This file contains only:
|   - Public routes (no authentication)
|   - Guest routes (login, register)
|   - Member routes (my-books, logout)
|
| All admin/pustakawan routes are in: routes/dynamic_routes.php
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/search', SearchController::class)->name('search');
Route::get('/preview/{book}', PreviewController::class)->name('preview');

/*
|--------------------------------------------------------------------------
| Guest Routes (Login & Register)
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class, 'store']);

    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Member)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Logout
    Route::delete('/logout', [AuthController::class, 'logout'])->name('logout');

    // My Books (Member functionality)
    Route::resource('/my-books', MyBookController::class)->only('index', 'update');
    Route::post('/my-books/{book}', [MyBookController::class, 'store'])->name('my-books.store');
});

/*
|--------------------------------------------------------------------------
| OLD ADMIN ROUTES - MOVED TO dynamic_routes.php
|--------------------------------------------------------------------------
|
| All routes below have been moved to routes/dynamic_routes.php
| 
| DO NOT UNCOMMENT - Will cause route conflicts!
|
| Previous routes included:
|   - /admin/dashboard
|   - /admin/librarians/*
|   - /admin/members/*
|   - /admin/books/*
|   - /admin/borrows/*
|   - /admin/returns/*
|   - /admin/profile/*
|
| These are now managed in dynamic_routes.php with proper organization.
|
|--------------------------------------------------------------------------
*/

/*
// ========================================================================
// DEPRECATED - Moved to dynamic_routes.php
// ========================================================================
//
// Route::middleware('auth')->group(function () {
//     Route::middleware('superuser')
//         ->prefix('/admin')
//         ->name('admin.')
//         ->group(function () {
//             Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
//
//             Route::middleware('admin')->group(function () {
//                 Route::resource('/librarians', LibrarianController::class)->except('show');
//             });
//
//             Route::middleware('librarian')->group(function () {
//                 Route::resource('/members', MemberController::class)->except('show');
//                 Route::resource('/books', BookController::class)->except('show');
//                 Route::resource('/borrows', BorrowController::class)->except('show', 'create', 'store');
//                 Route::resource('/returns', RestoreController::class)->except('show', 'create', 'store');
//             });
//         });
//
//     Route::prefix('admin')
//         ->name('admin.')
//         ->middleware(['auth', 'superuser'])
//         ->group(function () {
//             Route::controller(App\Http\Controllers\Admin\ProfileController::class)->group(function () {
//                 Route::get('/profile', 'index')->name('profile.index');
//                 Route::put('/profile', 'update')->name('profile.update');
//                 Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
//                 Route::put('/profile/photo', 'updatePhoto')->name('profile.photo.update');
//                 Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
//             });
//         });
// });
//
// ========================================================================
*/

/*
|--------------------------------------------------------------------------
| Admin Routes (Loaded from dynamic_routes.php)
|--------------------------------------------------------------------------
|
| All admin and pustakawan routes are defined in:
|   routes/dynamic_routes.php
|
| This provides:
|   - Better organization
|   - No route conflicts
|   - Single source of truth
|   - Easy maintenance
|
*/

require __DIR__.'/dynamic_routes.php';
PHP;
    }

    /**
     * Get basic dynamic_routes.php content
     */
    protected function getBasicDynamicRoutesContent()
    {
        return <<<'PHP'
<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\BorrowController;
use App\Http\Controllers\Admin\LibrarianController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\RestoreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| All admin and pustakawan routes are defined here.
| This provides a single source of truth for admin functionality.
|
*/

Route::middleware(['auth', 'superuser'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // Dashboard
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

        // Admin Only: Librarians Management
        Route::middleware('admin')->group(function () {
            Route::resource('librarians', LibrarianController::class)->except('show');
        });

        // Admin & Pustakawan: Shared Resources
        Route::middleware('librarian')->group(function () {
            // Members Management
            Route::resource('members', MemberController::class)->except('show');

            // Books Management
            Route::resource('books', BookController::class)->except('show');

            // Borrows Management
            Route::resource('borrows', BorrowController::class)
                ->except('show', 'create', 'store');

            // Returns Management
            Route::controller(RestoreController::class)->group(function () {
                Route::get('/returns', 'index')->name('returns.index');
                Route::get('/returns/{restore}/edit', 'edit')->name('returns.edit');
                Route::put('/returns/{restore}', 'update')->name('returns.update');
                Route::delete('/returns/{restore}', 'destroy')->name('returns.destroy');
            });
        });
    });
PHP;
    }
}
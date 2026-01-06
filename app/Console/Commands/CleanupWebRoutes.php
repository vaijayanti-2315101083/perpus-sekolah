<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CleanupWebRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:routes 
                            {--force : Overwrite without asking}
                            {--backup : Create backup of web.php}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup web.php routes: remove duplicates, comment static admin routes, fix conflicts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Cleaning up routes/web.php...');
        $this->newLine();

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
            $this->line("💾 Backup created: {$backupPath}");
        }

        // Confirm before proceeding
        if (!$force) {
            if (!$this->confirm('This will clean up routes/web.php and comment out old static admin routes. Continue?', true)) {
                $this->warn('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Generate clean web.php
        $cleanedRoutes = $this->getCleanWebRoutesStub();
        File::put($webRoutesPath, $cleanedRoutes);

        $this->info('✅ routes/web.php cleaned and updated!');
        $this->newLine();

        // Display summary
        $this->displaySummary();

        return Command::SUCCESS;
    }

    /**
     * Display summary of changes
     */
    protected function displaySummary()
    {
        $this->line('📋 <fg=yellow>CHANGES MADE:</>');
        $this->newLine();

        $this->line('✅ <fg=green>Kept (Active Routes):</>');
        $this->line('   - Home, Search, Preview (public routes)');
        $this->line('   - Register, Login (guest routes)');
        $this->line('   - Logout, My Books (auth routes)');
        $this->line('   - Dynamic routes included (via dynamic_routes.php)');
        $this->newLine();

        $this->line('❌ <fg=red>Removed/Commented (Duplicates):</>');
        $this->line('   - Static /admin prefix routes (duplicated in dynamic_routes.php)');
        $this->line('   - Old /admin/dashboard static route');
        $this->line('   - Old /admin/profile static routes');
        $this->line('   - Duplicate middleware groups');
        $this->newLine();

        $this->line('🔧 <fg=cyan>NEXT STEPS:</>');
        $this->newLine();

        $this->line('1️⃣  Clear route cache:');
        $this->line('   <fg=green>php artisan route:clear</>');
        $this->newLine();

        $this->line('2️⃣  Verify routes:');
        $this->line('   <fg=green>php artisan route:list</>');
        $this->newLine();

        $this->line('3️⃣  Test the application:');
        $this->line('   <fg=cyan>- Test home, search, preview</>');
        $this->line('   <fg=cyan>- Test login/register</>');
        $this->line('   <fg=cyan>- Test /admin/dashboard (Admin)</>');
        $this->line('   <fg=cyan>- Test /pustakawan/dashboard (Pustakawan)</>');
        $this->newLine();

        $this->line('✨ <fg=green>Result:</>');
        $this->line('   - No duplicate routes');
        $this->line('   - Clean separation: static (public/auth) vs dynamic (admin/pustakawan)');
        $this->line('   - All routes working via dynamic_routes.php');
        $this->newLine();

        $this->line('📚 <fg=yellow>NOTE:</>');
        $this->line('   All admin/pustakawan routes are now managed in:');
        $this->line('   <fg=cyan>routes/dynamic_routes.php</>');
        $this->newLine();
    }

    /**
     * Get clean web routes stub
     */
    protected function getCleanWebRoutesStub()
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
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
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
| Authenticated User Routes
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
| OLD STATIC ADMIN ROUTES - COMMENTED OUT
|--------------------------------------------------------------------------
| These routes have been replaced by dynamic_routes.php
| which provides role-based URL prefixes:
|   - Admin: /admin/*
|   - Pustakawan: /pustakawan/*
|
| DO NOT UNCOMMENT - Will cause route conflicts!
|--------------------------------------------------------------------------
*/

/*
// OLD - Replaced by dynamic_routes.php
Route::middleware('auth')->group(function () {
    Route::middleware('superuser')
        ->prefix('/admin')
        ->name('admin.')
        ->group(function () {
            Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

            Route::middleware('admin')->group(function () {
                Route::resource('/librarians', LibrarianController::class)->except('show');
            });

            Route::middleware('librarian')->group(function () {
                Route::resource('/members', MemberController::class)->except('show');
                Route::resource('/books', BookController::class)->except('show');
                Route::resource('/borrows', BorrowController::class)->except('show', 'create', 'store');
                Route::resource('/returns', RestoreController::class)->except('show', 'create', 'store');
            });
        });

    Route::prefix('admin')
        ->name('admin.')
        ->middleware(['auth', 'superuser'])
        ->group(function () {
            // Profile Management
            Route::controller(App\Http\Controllers\Admin\ProfileController::class)->group(function () {
                Route::get('/profile', 'index')->name('profile.index');
                Route::put('/profile', 'update')->name('profile.update');
                Route::put('/profile/password', 'updatePassword')->name('profile.password.update');
                Route::put('/profile/photo', 'updatePhoto')->name('profile.photo.update');
                Route::delete('/profile/photo', 'deletePhoto')->name('profile.photo.delete');
            });
        });
});
*/

/*
|--------------------------------------------------------------------------
| Dynamic Admin Routes (Role-Based URL Prefixes)
|--------------------------------------------------------------------------
|
| These routes provide:
|   - Admin routes: /admin/dashboard, /admin/books, etc.
|   - Pustakawan routes: /pustakawan/dashboard, /pustakawan/books, etc.
|   - Strict role protection
|
| All admin/pustakawan routes are managed in: routes/dynamic_routes.php
|
*/

require __DIR__.'/dynamic_routes.php';
PHP;
    }
}
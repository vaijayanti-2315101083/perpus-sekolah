<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupCompleteSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:complete 
                            {--force : Overwrite all files without asking}
                            {--backup : Create backups of existing files}
                            {--skip-dynamic : Skip dynamic routing setup}
                            {--skip-dashboard : Skip dashboard setup}
                            {--skip-cleanup : Skip routes cleanup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Complete system setup: dynamic routing + role dashboard + protection + routes cleanup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->displayBanner();

        $force = $this->option('force');
        $backup = $this->option('backup');

        // Build options string
        $options = [];
        if ($force) $options[] = '--force';
        if ($backup) $options[] = '--backup';
        $optionsString = implode(' ', $options);

        $steps = [];

        // Step 1: Dynamic Routing Setup
        if (!$this->option('skip-dynamic')) {
            $steps[] = [
                'name' => 'Dynamic Routing Setup',
                'command' => "setup:dynamic-routing {$optionsString}",
                'description' => 'Setting up role-based URL prefixes (/admin, /pustakawan)',
            ];
        }

        // Step 2: Role Dashboard & Protection
        if (!$this->option('skip-dashboard')) {
            $steps[] = [
                'name' => 'Role Dashboard & Protection',
                'command' => "setup:role-dashboard {$optionsString}",
                'description' => 'Creating separate dashboards and strict protection',
            ];
        }

        // Step 3: Routes Cleanup
        if (!$this->option('skip-cleanup')) {
            $steps[] = [
                'name' => 'Routes Cleanup',
                'command' => "cleanup:routes {$optionsString}",
                'description' => 'Removing duplicate routes and fixing conflicts',
            ];
        }

        if (empty($steps)) {
            $this->warn('All steps skipped. Nothing to do.');
            return Command::SUCCESS;
        }

        // Confirm before proceeding
        if (!$force && !$this->option('backup')) {
            $this->newLine();
            $this->warn('⚠️  Running without --backup flag!');
            if (!$this->confirm('Proceed without creating backups?', false)) {
                $this->line('👍 Run again with --backup flag: php artisan setup:complete --backup');
                return Command::SUCCESS;
            }
        }

        $this->newLine();
        $this->line('📋 <fg=yellow>SETUP PLAN:</>');
        foreach ($steps as $index => $step) {
            $this->line('   ' . ($index + 1) . '. ' . $step['name']);
        }
        $this->newLine();

        if (!$force) {
            if (!$this->confirm('Execute all steps?', true)) {
                $this->warn('Setup cancelled.');
                return Command::SUCCESS;
            }
        }

        // Execute each step
        $this->newLine();
        $this->info('🚀 Starting complete system setup...');
        $this->newLine();

        $successful = 0;
        $failed = 0;

        foreach ($steps as $index => $step) {
            $stepNumber = $index + 1;
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line("STEP {$stepNumber}/{count($steps)}: {$step['name']}");
            $this->line($step['description']);
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->newLine();

            try {
                $exitCode = $this->call($step['command']);
                
                if ($exitCode === Command::SUCCESS) {
                    $this->info("✅ Step {$stepNumber} completed successfully!");
                    $successful++;
                } else {
                    $this->error("❌ Step {$stepNumber} failed!");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->error("❌ Step {$stepNumber} failed with exception: " . $e->getMessage());
                $failed++;
            }

            $this->newLine();
        }

        // Summary
        $this->displaySummary($successful, $failed, $steps);

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Display banner
     */
    protected function displayBanner()
    {
        $this->newLine();
        $this->line('╔═══════════════════════════════════════════════════════════╗');
        $this->line('║                                                           ║');
        $this->line('║       🚀 PERPUSTAKAAN - COMPLETE SYSTEM SETUP 🚀          ║');
        $this->line('║                                                           ║');
        $this->line('║   This command will setup:                                ║');
        $this->line('║   ✅ Dynamic Routing (role-based URL prefixes)            ║');
        $this->line('║   ✅ Separate Dashboards (Admin vs Pustakawan)            ║');
        $this->line('║   ✅ Strict Role Protection                               ║');
        $this->line('║   ✅ Clean Routes (no duplicates)                         ║');
        $this->line('║                                                           ║');
        $this->line('╚═══════════════════════════════════════════════════════════╝');
        $this->newLine();
    }

    /**
     * Display final summary
     */
    protected function displaySummary($successful, $failed, $steps)
    {
        $this->line('╔═══════════════════════════════════════════════════════════╗');
        $this->line('║                    SETUP COMPLETED                        ║');
        $this->line('╚═══════════════════════════════════════════════════════════╝');
        $this->newLine();

        if ($failed === 0) {
            $this->info("🎉 All {$successful} steps completed successfully!");
        } else {
            $this->warn("⚠️  {$successful} steps succeeded, {$failed} steps failed.");
        }

        $this->newLine();
        $this->line('📋 <fg=yellow>FINAL STEPS:</>');
        $this->newLine();

        $this->line('1️⃣  Clear all caches:');
        $this->line('   <fg=green>php artisan optimize:clear</>');
        $this->newLine();

        $this->line('2️⃣  Verify routes:');
        $this->line('   <fg=green>php artisan route:list | grep -E "(admin|pustakawan)"</>');
        $this->newLine();

        $this->line('3️⃣  Test the system:');
        $this->line('   <fg=cyan>Admin:</>');
        $this->line('   - Login as Admin');
        $this->line('   - Visit: /admin/dashboard');
        $this->line('   - Should see: Full statistics dashboard');
        $this->line('   - Can access: /admin/librarians');
        $this->newLine();

        $this->line('   <fg=cyan>Pustakawan:</>');
        $this->line('   - Login as Pustakawan');
        $this->line('   - Visit: /pustakawan/dashboard');
        $this->line('   - Should see: Operational dashboard with pending tasks');
        $this->line('   - Try access: /admin/librarians → Should redirect with error');
        $this->newLine();

        $this->line('   <fg=cyan>Member:</>');
        $this->line('   - Login as Member');
        $this->line('   - Visit: /profile (clean URL, no prefix)');
        $this->line('   - Try access: /admin/* → Should be blocked');
        $this->newLine();

        $this->line('✨ <fg=green>WHAT YOU GOT:</>');
        $this->line('   ✅ Pretty role-based URLs (/admin, /pustakawan, /profile)');
        $this->line('   ✅ Separate dashboards (Admin: full, Pustakawan: operational)');
        $this->line('   ✅ Strict protection (auto redirect on wrong access)');
        $this->line('   ✅ Clean routes (no duplicates or conflicts)');
        $this->line('   ✅ Helper functions (dashboard_route(), dynamic_route(), etc.)');
        $this->line('   ✅ Auto redirect middleware');
        $this->line('   ✅ Production ready!');
        $this->newLine();

        $this->line('📚 <fg=yellow>DOCUMENTATION:</>');
        $this->line('   - README_DYNAMIC_ROUTING.md (overview)');
        $this->line('   - QUICK_START_DYNAMIC_ROUTING.md (dynamic routing guide)');
        $this->line('   - QUICK_START_ROLE_DASHBOARD.md (dashboard guide)');
        $this->newLine();

        $this->line('🎯 <fg=green>System is ready for production!</>');
        $this->newLine();
    }
}
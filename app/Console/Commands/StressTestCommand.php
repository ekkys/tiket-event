<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Support\Str;

class StressTestCommand extends Command
{
    protected $signature = 'app:stress-test {users=4000} {--url=http://127.0.0.1:8000} {--force}';
    protected $description = 'Simulate high concurrency registration load';

    public function handle()
    {
        $usersCount = $this->argument('users');
        $baseUrl = $this->option('url');
        
        $this->info("🚀 Starting stress test for {$usersCount} users against {$baseUrl}...");

        // 1. Reset Counter dan Data jika perlu
        if ($this->option('force') || $this->confirm('Reset all registrations and set event quota to ' . $usersCount . '?')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Registration::truncate();
            DB::table('tickets')->truncate();
            DB::table('scan_logs')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            
            $event = Event::first();
            $event->update([
                'quota'            => $usersCount,
                'registered_count' => 0,
                'is_active'        => true,
            ]);
            $this->warn("♻️ Environment reset. Quota set to {$usersCount}.");
        }

        $this->info("⏳ Sending requests (in batches of 50 to maintain stability)...");

        $start = microtime(true);
        $success = 0;
        $failed = 0;
        
        $batchSize = 50;
        $totalBatches = ceil($usersCount / $batchSize);

        $bar = $this->output->createProgressBar($usersCount);
        $bar->start();

        for($i = 0; $i < $totalBatches; $i++) {
            $responses = Http::pool(fn ($pool) => 
                collect(range(1, $batchSize))->map(function() use ($pool, $baseUrl) {
                    $name = "Tester " . Str::random(8);
                    return $pool->withHeaders([
                        'X-Stress-Test' => config('app.key'),
                        'Accept'        => 'application/json',
                    ])->post("{$baseUrl}/daftar", [
                        'full_name'   => $name,
                        'email'       => Str::lower(Str::random(10)) . "@example.com",
                        'phone'       => "08" . rand(1000000000, 9999999999),
                        'institution' => 'Load Test Lab',
                    ]);
                })
            );

            foreach($responses as $response) {
                if ($response && ($response->successful() || $response->status() === 302)) {
                    $success++;
                } else {
                    $failed++;
                    // $this->error($response->body());
                }
                $bar->advance();
            }
        }

        $bar->finish();
        echo "\n";

        $end = microtime(true);
        $duration = round($end - $start, 2);

        $this->info("✅ Finished in {$duration} seconds.");
        $this->info("📊 Success: {$success} | Failed: {$failed}");

        // 2. Verifikasi Integritas Data
        $finalCount = Registration::count();
        $dbCounter = Event::first()->registered_count;

        $this->comment("\n🔍 --- Integrity Verification ---");
        $this->comment("Actual DB rows: {$finalCount}");
        $this->comment("Event counter : {$dbCounter}");
        
        if ($finalCount == $usersCount && $dbCounter == $usersCount) {
            $this->info("🏆 SUCCESS: No over-selling detected. Atomic integrity maintained.");
        } else if ($finalCount > $usersCount) {
            $this->error("❌ FAILURE: OVER-SELLING DETECTED! Found {$finalCount} rows for quota {$usersCount}.");
        } else {
            $this->warn("⚠️ WARNING: Under-selling or early failures. Expected {$usersCount}, got {$finalCount}.");
        }
    }
}

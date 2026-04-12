<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Models\Registration;
use App\Jobs\GenerateTicketJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FastRegisterCommand extends Command
{
    protected $signature = 'app:fast-register {count=100}';
    protected $description = 'Run registration logic internally as fast as possible';

    public function handle()
    {
        $count = $this->argument('count');
        $event = Event::getActive();

        for($i = 0; $i < $count; $i++) {
            $data = [
                'full_name'   => "Concurrent User " . Str::random(5),
                'email'       => Str::random(10) . "@stress.test",
                'phone'       => "08" . rand(1000000000, 9999999999),
                'institution' => 'High Concurrency Lab',
            ];

            try {
                DB::transaction(function () use ($data, $event) {
                    $currentEvent = Event::lockForUpdate()->find($event->id);

                    if (!$currentEvent->isQuotaAvailable()) {
                        throw new \Exception("Quota Full");
                    }

                    $registration = Registration::create($data + [
                        'event_id'          => $event->id,
                        'registration_code' => Registration::generateRegistrationCode(),
                        'payment_status'    => 'free',
                        'amount_paid'       => 0,
                    ]);

                    $currentEvent->incrementRegistered();
                    
                    // Kita matikan dispatch queue untuk speed test
                    // GenerateTicketJob::dispatch($registration);
                });
            } catch (\Exception $e) {
                if ($e->getMessage() === 'Quota Full') {
                    $this->warn("⚠️ Quota Full reached.");
                    break;
                }
                $this->error($e->getMessage());
            }
        }
    }
}

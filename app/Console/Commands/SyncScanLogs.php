<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncScanLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:scan-logs';
    protected $description = 'Sinkronisasi ticket_id pada tabel scan_logs';

    public function handle()
    {
        $this->info('Memulai sinkronisasi scan_logs...');
        $logs = \App\Models\ScanLog::whereNull('ticket_id')->get();
        $count = 0;

        foreach ($logs as $log) {
            $ticket = \App\Models\Ticket::where('token', $log->token)->first();
            
            if (!$ticket) {
                $registration = \App\Models\Registration::where('registration_code', $log->token)->first();
                if ($registration) {
                    $ticket = $registration->ticket;
                }
            }
            
            if ($ticket) {
                $log->update(['ticket_id' => $ticket->id]);
                $count++;
            }
        }

        $this->info("Berhasil menyinkronkan $count log.");
    }
}

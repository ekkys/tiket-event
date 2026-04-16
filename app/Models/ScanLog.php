<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    protected $fillable = [
        'token', 'ticket_id', 'success', 'message', 'scanner_name', 'ip_address'
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEntry extends Model
{
    protected $table = 'logs';

    public $timestamps = false;

    protected $fillable = [
        'ip_address',
        'request_date',
        'url',
        'os',
        'architecture',
        'browser',
        'user_agent',
    ];

    protected $casts = [
        'request_date' => 'datetime',
    ];
}

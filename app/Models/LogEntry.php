<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogEntry extends Model
{
    protected $fillable = [
        'request_date',
        'url',
        'os',
        'architecture',
        'browser',
        'user_agent'
    ];
}

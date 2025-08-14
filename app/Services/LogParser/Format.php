<?php

namespace App\Services\LogParser;

use App\Contracts\LogParser\FormatInterface;

class Format implements FormatInterface
{
    public function getIdentifiers(): array
    {
        return ['ip_address', 'request_date', 'url', 'user_agent'];
    }
}
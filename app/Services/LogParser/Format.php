<?php

namespace App\Services\LogParser;

use App\Contracts\LogParser\FormatInterface;

class Format implements FormatInterface
{
    public function getPattern(): string
    {
        return '/^([\w\.\:]+) - - \[([^\]]+)\] "\S+ ([^"]+)" \d+ \d+ "[^"]*" "([^"]+)"$/';
    }
}
<?php

namespace App\Services\LogParser;

use App\Contracts\LogParser\PatternInterface;

class Pattern implements PatternInterface
{
    public function getPattern(): string
    {
        return '/^([\d\.]+) - - \[([^\]]+)\] "\S+ ([^"]+) HTTP\/[0-9.]+" \d+ \d+ "[^"]*" "([^"]+)"$/';
    }
}
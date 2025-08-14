<?php

namespace App\Contracts\LogParser;

interface PatternInterface
{
    public function getIdentifiers(): array;
}
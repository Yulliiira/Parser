<?php

namespace App\Contracts\LogParser;

interface PatternInterface
{
    public function getPattern(): string;
}
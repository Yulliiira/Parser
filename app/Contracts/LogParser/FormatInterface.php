<?php

namespace App\Contracts\LogParser;

interface FormatInterface
{
    public function getPattern(): string;
}
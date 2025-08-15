<?php

namespace App\Contracts;

use App\DTO\LogDTO;

interface LogParserServiceInterface
{
    public function stringParse(string $parseParseLine): ?LogDTO;
    public function parseAndSave(string $line): ?LogDTO;

}

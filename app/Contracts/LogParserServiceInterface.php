<?php

namespace App\Contracts;

use App\DTO\LogEntryDTO;

interface LogParserServiceInterface
{
    public function stringParse(string $parseParseLine): ?LogEntryDTO;
    public function parseAndSave(string $line): ?LogEntryDTO;

}

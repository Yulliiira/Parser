<?php

namespace App\Contracts;

use App\DTO\LogEntryDTO;
use App\Models\LogEntry;

interface LogParserServiceInterface
{
    /**
     * @param ?string $parseParseLine
     * @return LogEntryDTO|null
     */
    public function stringParse(string $parseParseLine): ?LogEntryDTO;

    /**
     * @param string $line
     * @return ?LogEntryDTO|null
     */
    public function parseAndSave(string $line): ?LogEntryDTO;

}

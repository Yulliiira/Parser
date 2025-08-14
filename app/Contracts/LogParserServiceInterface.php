<?php

namespace App\Contracts;

use App\DTO\LogEntryDTO;

interface LogParserServiceInterface
{
    /**
     * @param ?string $parseLine
     * @return LogEntryDTO|null
     */
    public function stringParse(string $parseLine): ?LogEntryDTO;

    /**
     * @param string $line
     * @return LogEntryDTO|null
     */
    public function parseAndSave(string $line): ?LogEntryDTO;

}

<?php

namespace App\Contracts;

use App\DTO\LogEntryDTO;

interface LogRepositoryInterface
{
    public function getLogs();

    public function postLogs(LogEntryDTO $dto);
}
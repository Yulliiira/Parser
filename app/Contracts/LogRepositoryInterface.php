<?php

namespace App\Contracts;

use App\DTO\LogEntryDTO;
use App\Models\LogEntry;
use Illuminate\Database\Eloquent\Collection;

interface LogRepositoryInterface
{
    public function getLogs(): Collection;
    public function postLogs(LogEntryDTO $dto): LogEntry;
}


<?php

namespace App\Repositories;

use App\Contracts\LogRepositoryInterface;
use App\DTO\LogEntryDTO;
use App\Models\LogEntry;

class LogRepository implements LogRepositoryInterface
{
    public function getLogs()
    {
        return LogEntry::all();
    }

    public function postLogs(LogEntryDTO $dto)
    {
        return LogEntry::create([
            'ip_address' => $dto->ip_address,
            'request_date' => $dto->request_date,
            'url' => $dto->url,
            'os' => $dto->os,
            'architecture' => $dto->architecture,
            'browser' => $dto->browser,
            'user_agent' => $dto->user_agent
        ]);
    }
}
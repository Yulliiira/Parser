<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

use App\Contracts\LogRepositoryInterface;
use App\DTO\LogEntryDTO;
use App\Models\LogEntry;

class LogRepository implements LogRepositoryInterface
{
    /**
     * показывает логи
     * @return Collection
     */
    public function getLogs(): Collection
    {
        return LogEntry::all();
    }

    /**
     * сохраняет логи
     * @param LogEntryDTO $dto
     * @return mixed
     */
    public function postLogs(LogEntryDTO $dto): LogEntry
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

<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

use App\Contracts\LogRepositoryInterface;
use App\DTO\LogDTO;
use App\Models\Log;

class LogRepository implements LogRepositoryInterface
{
    /**
     * показывает логи
     * @return Collection
     */
    public function getLogs(): Collection
    {
        return Log::all();
    }

    /**
     * сохраняет логи
     * @param LogDTO $dto
     * @return mixed
     */
    public function postLogs(LogDTO $dto): Log
    {
        return Log::create([
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

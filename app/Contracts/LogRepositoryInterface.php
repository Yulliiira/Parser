<?php

namespace App\Contracts;

use App\DTO\LogDTO;
use App\Models\Log;
use Illuminate\Database\Eloquent\Collection;

interface LogRepositoryInterface
{
    public function getLogs(): Collection;
    public function postLogs(LogDTO $dto): Log;
}


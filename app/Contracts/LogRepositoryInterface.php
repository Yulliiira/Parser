<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

use App\DTO\LogDTO;
use App\Models\Log;

interface LogRepositoryInterface
{
    public function getLogs(): Collection;
    public function postLogs(LogDTO $dto): Log;
}


<?php

namespace App\Contracts;

use App\DTO\LogEntryDTO;

interface LogRepositoryInterface
{
    public function getLogs();
    public function postLogs(LogEntryDTO $dto);
    public function getLogsFiltered($query, array $filters, array $sort = []);
    public function getRawGraphData(array $filters = []): array;
}

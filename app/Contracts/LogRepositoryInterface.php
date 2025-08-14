<?php

namespace App\Contracts;

use App\DTO\LogEntryDTO;
use Illuminate\Database\Eloquent\Collection;

interface LogRepositoryInterface
{
    /**
     * @return mixed
     */
    public function getLogs();

    /**
     * @param LogEntryDTO $dto
     * @return mixed
     */
    public function postLogs(LogEntryDTO $dto);

    /**
     * @param array $filters
     * @param array $sort
     * @return mixed
     */
    public function getLogsFiltered(array $filters, array $sort = []);

    /**
     * @param array $filters
     * @return array
     */
    public function getRawGraphData(array $filters = []): Collection;
}

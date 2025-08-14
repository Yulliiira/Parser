<?php

namespace App\Contracts;

interface LogAnalyticsRepositoryInterface
{
    public function getLogsFiltered(array $filters = [], array $sort = []);
    public function getRawGraphData(array $filters = []): array;
}

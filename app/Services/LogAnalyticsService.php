<?php

namespace App\Services;

use App\Contracts\LogAnalyticsRepositoryInterface;
use App\Contracts\LogAnalyticsServiceInterface;

class LogAnalyticsService implements LogAnalyticsServiceInterface
{
    public function __construct(private LogAnalyticsRepositoryInterface $logAnalyticsRepository) {}

    public function getGraphData(array $filters = []): array
    {
        return $this->logAnalyticsRepository->getRawGraphData($filters);
    }

    public function getLogsFiltered(array $filters = [], array $sort = [])
    {
        return $this->logAnalyticsRepository->getLogsFiltered($filters, $sort);
    }
}
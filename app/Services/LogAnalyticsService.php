<?php

namespace App\Services;

use App\Contracts\LogAnalyticsServiceInterface;
use App\Contracts\LogRepositoryInterface;

class LogAnalyticsService implements LogAnalyticsServiceInterface
{
    public function __construct(private LogRepositoryInterface $logRepository)
    {
    }

    public function getGraphData(array $filters = []): array
    {
        return $this->logRepository->getRawGraphData($filters);
    }
}
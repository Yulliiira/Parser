<?php

namespace App\Contracts;

interface LogAnalyticsServiceInterface
{
    public function getGraphData(array $filters = []): array;
}
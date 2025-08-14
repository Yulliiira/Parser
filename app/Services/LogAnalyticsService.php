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
        $rows = $this->logRepository->getRawGraphData($filters);

        $dates = array_unique($rows->pluck('date')->toArray());

        $sum = [];
        $counts = [];// browser -> date -> count
        $totalsByDate = [];// date -> total

        foreach ($rows as $r) {
//                $browser = trim($r->browser);
            $sum[$r->browser] = ($sum[$r->browser] ?? 0) + $r->count;
            $counts[$r->browser][$r->date] = $r->count;
            $totalsByDate[$r->date] = ($totalsByDate[$r->date] ?? 0) + $r->count;
        }
        arsort($sum);
        $topBrowsers = array_slice(array_keys($sum), 0, 3);

        //формируем массив по датам
        $browsersData = [];
        foreach ($topBrowsers as $browser) {
            $series = [];
            foreach ($dates as $date) {
                $count = $counts[$browser][$date] ?? 0;
                $total = $totalsByDate[$date] ?? 0;
                $percent = $total ? round($count / $total * 100, 2) : 0;
                $series[] = round($percent, 2);
            }
            $browsersData[$browser] = $series;
        }
//        dd($rows);
        return [
            'dates' => $dates,
            'browsers' => $browsersData
        ];
    }
}
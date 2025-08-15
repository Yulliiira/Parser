<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

use App\Contracts\LogAnalyticsRepositoryInterface;
use App\Models\Log;

class LogAnalyticsRepository implements LogAnalyticsRepositoryInterface
{
    /**
     *  Возвращает отфильтрованные логи
     * @param array $filters
     * @param array $sort
     * @return Collection|mixed
     */
    public function getLogsFiltered(array $filters = [], array $sort = [])
    {
        $daily = Log::selectRaw('DATE(request_date) as d_date, COUNT(*) as request_count')
            ->groupByRaw('DATE(request_date)');

        $topUrl = Log::selectRaw('DATE(request_date) as u_date, url, COUNT(*) as cnt')
            ->groupByRaw('DATE(request_date), url')
            ->orderByRaw('cnt DESC');

        $topBrowser = Log::selectRaw('DATE(request_date) as b_date, browser, COUNT(*) as cnt')
            ->groupByRaw('DATE(request_date), browser')
            ->orderByRaw('cnt DESC');

        $query = DB::query()
            ->fromSub($daily, 'daily')
            ->leftJoinSub($topUrl, 'u', 'u.u_date', '=', 'daily.d_date')
            ->leftJoinSub($topBrowser, 'b', 'b.b_date', '=', 'daily.d_date')
            ->select([
                'daily.d_date as date',
                'daily.request_count',
                'u.url',
                'b.browser'
            ]);

        $this->applyFilters($query, $filters);

        foreach ($sort as $key => $value) {
            if ($key === 'date') {
                $query->orderBy('daily.d_date', $value);
            }
        }

        return $query->get();
    }

    /**
     *  Возвращает данные для графика по логам запросов
     * @param array $filters
     * @return array
     */
    public function getRawGraphData(array $filters = []): array
    {
        $query = Log::query()
            ->selectRaw('
            DATE(request_date) as date,
            browser,
            COUNT(*) as count
        ');

        $this->applyFilters($query, $filters);
        $rows = $query
            ->groupBy('date', 'browser')
            ->orderBy('date')
            ->get();

        $dates = $rows->pluck('date')->unique()->values()->toArray();
        $totalsByDate = [];
        $counts = [];
        //подсчет запросов
        foreach ($rows as $r) {
            $totalsByDate[$r->date] = ($totalsByDate[$r->date] ?? 0) + $r->count;
            $counts[$r->browser][$r->date] = $r->count;
        }

        $topBrowsers = collect($counts)
            ->map(fn($data) => array_sum($data))
            ->sortDesc()
            ->keys()
            ->take(3)
            ->toArray();
        //формируем данные
        $browsersData = [];
        foreach ($topBrowsers as $browser) {
            $series = [];
            foreach ($dates as $date) {
                $count = $counts[$browser][$date] ?? 0;
                $total = $totalsByDate[$date] ?? 0;
                $series[] = $total ? round($count / $total * 100, 2) : 0;
            }
            $browsersData[$browser] = $series;
        }

        return [
            'dates' => $dates,
            'requests' => array_values($totalsByDate),
            'browsers' => $browsersData
        ];
    }

    /**
     * фильтры
     * @param $query
     * @param $filters
     * @return void
     */
    private function applyFilters($query, $filters): void
    {
        if (!empty($filters['os'])) {
            $query->where('os', $filters['os']);
        }
        if (!empty($filters['architecture'])) {
            $query->where('architecture', $filters['architecture']);
        }
        if (!empty($filters['date_from'])) {
            $query->whereDate('request_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->whereDate('request_date', '<=', $filters['date_to']);
        }
    }
}

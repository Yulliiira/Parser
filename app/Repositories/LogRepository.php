<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

use App\Contracts\LogRepositoryInterface;
use App\DTO\LogEntryDTO;
use App\Models\LogEntry;

class LogRepository implements LogRepositoryInterface
{
    public function getLogs()
    {
        return LogEntry::all();
    }

    public function postLogs(LogEntryDTO $dto)
    {
        return LogEntry::create([
            'ip_address' => $dto->ip_address,
            'request_date' => $dto->request_date,
            'url' => $dto->url,
            'os' => $dto->os,
            'architecture' => $dto->architecture,
            'browser' => $dto->browser,
            'user_agent' => $dto->user_agent
        ]);
    }

    /**
     *  Возвращает отфильтрованные логи
     * @param $query
     * @param array $filters
     * @param array $sort
     * @return Collection|mixed
     */
    public function getLogsFiltered($query = null, array $filters = [], array $sort = [])
    {
        $query = LogEntry::query();
        $this->applyFilters($query, $filters);

        foreach ($sort as $key => $value) {
            if ($key === 'date') {
                $query->orderBy($key, $value);
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
        $query = LogEntry::query()
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

    private function applyFilters($query, array $filters): void
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

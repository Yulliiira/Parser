<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;

use App\Contracts\LogRepositoryInterface;
use App\DTO\LogEntryDTO;
use App\Models\LogEntry;
use Illuminate\Support\Facades\DB;

class LogRepository implements LogRepositoryInterface
{
    /**
     * @return Collection|mixed
     */
    public function getLogs()
    {
        return LogEntry::all();
    }

    /**
     * @param LogEntryDTO $dto
     * @return mixed
     */
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
     * Возвращает отфильтрованные логи
     * @param array $filters
     * @param array $sort
     * @return Collection|mixed
     */
    public function getLogsFiltered(array $filters, array $sort = [])
    {
        //запрос к модели
        $query = LogEntry::query()
            ->selectRaw('DATE(request_date) as date')
            ->selectRaw('COUNT(*) as requests_count')
            ->selectRaw('COUNT(DISTINCT url) as urls_count')
            ->addSelect([
                'top_browser' => LogEntry::select('browser')
                    ->whereColumn(DB::raw('DATE(request_date)'), '=', DB::raw('DATE(log_entries.request_date)'))
                    ->groupBy('browser')
                    ->orderByRaw('COUNT(*) DESC')
                    ->limit(1)
            ]);
        // Применение фильтр
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
        foreach ($sort as $key => $value) {
            if ($key === 'date') {
                $query->orderBy($key, $value);
            }
        }
        // Выполняем запрос
        return $query->get();
    }

    /**
     *  Возвращает данные для графика по логам запросов
     * @param array $filters
     * @return array
     */
    public function getGraphData(array $filters = []): array
    {
        // Получаем массив всех дат
        $dates = LogEntry::select('date', 'requests_count')
            ->pluck('date')
            ->toArray();

        //получаем данные по каждому браузеру за кждую дату
        $rows = LogEntry::selectRaw('DATE(request_date) as date, browser, COUNT(*) as count')
            ->when(!empty($filters['os']), function ($query) use ($filters) {
                $query->where('os', $filters['os']);
            })
            ->when(!empty($filters['architecture']), function ($query) use ($filters) {
                $query->where('architecture', $filters['architecture']);
            })
            ->when(!empty($filters['date_from']), function ($query) use ($filters) {
                $query->whereDate('request_date', '>=', $filters['date_from']);
            })
            ->when(!empty($filters['date_to']), function ($query) use ($filters) {
                $query->whereDate('request_date', '<=', $filters['date_to']);
            })
            ->groupBy('date', 'browser')
            ->orderBy('date')
            ->get();;

        //подсчёт количества по браузерам
        $sum = [];
        $counts = [];// browser -> date -> count
        $totalsByDate = [];// date -> total

        foreach ($rows as $r) {
//            $browser = trim($r->browser);
            $sum[$r->browser] = ($sum[$r->browser] ?? 0) + $r->count;
            $counts[$r->browser][$r->date] = $r->count;
            $totalsByDate[$r->date] = ($totalsByDate[$r->date] ?? 0) + $r->count;
        }

        arsort($sum);
        $topBrowsers = array_slice(array_keys($sum), 0, 3);

        //формируем массив процентов по датам
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

        return [
            'dates' => $dates,
            'browsers' => $browsersData
        ];
    }
}

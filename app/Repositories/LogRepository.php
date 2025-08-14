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
     * Возвращает отфильтрованные логи
     * @param array $filters
     * @param array $sort
     * @return Collection|mixed
     */
    public function getLogsFiltered(array $filters, array $sort = [])
    {
        //запрос к модели
        $query = LogEntry::query();

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
     * @return Collection
     */
    public function getRawGraphData(array $filters = []): Collection
    {
        // Получаем массив всех дат
        $query = LogEntry::query()
            ->selectRaw('DATE(request_date) as date, browser, COUNT(*) as count');

            if (!empty($filters['os'])) {
                $query->where('os', $filters['os']);
            };
            if (!empty($filters['architecture'])) {
                $query->where('architecture', $filters['architecture']);
            };
            if (!empty($filters['date_from'])) {
                $query->whereDate('request_date', '>=', $filters['date_from']);
            };
            if (!empty($filters['date_to'])) {
                $query->whereDate('request_date', '<=', $filters['date_to']);
            };

        return $query
            ->groupBy('date', 'browser')
            ->orderBy('date')
            ->get();
    }
}

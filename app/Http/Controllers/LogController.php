<?php

namespace App\Http\Controllers;

use App\Contracts\LogRepositoryInterface;
use App\Services\LogAnalyticsService;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Http\Request;
use App\Services\LogParserService;


class LogController extends Controller
{
    public function __construct(private LogRepositoryInterface $logRepository,
        private LogAnalyticsService $logAnalyticsService,
        private LogParserService $logParserService,)
    {
    }


    /**
     * API для парсинга одной строки лога
     */
    public function index(Request $request)
    {
        $line = $request->input('line');

        if (!$line) {
            return response()->json(['error' => 'Line is required'], 400);
        }

        $model = $this->logParserService->stringParse($line);

        if (!$model) {
            return response()->json(['error' => 'Invalid log format'], 400);
        }

        return response()->json($model);
    }

    public function dashboard(Request $request)
    {
        $filters = [
            'os' => $request->input('os'),
            'architecture' => $request->input('architecture'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
        ];
//        //фильтры для теста
//        $filters = [
//            'os' => null,
//            'architecture' => null,
//            'date_from' => null,
//            'date_to' => null,
//        ];

        // Данные таблицы
        $logs = $this->logRepository->getLogsFiltered($filters);
        $graphData = $this->logAnalyticsService->getGraphData($filters);

        // График 1: число запросов по датам
        $requestsChart = new Chart;
        if (!empty($graphData['dates'])) {
            $requestsChart->labels(array_values($graphData['dates']));
            $requestsChart->dataset('Запросы', 'line', array_map(function($date) use ($logs) {
                return $logs->firstWhere('date', $date)->requests_count ?? 0;
            }, $graphData['dates']))->backgroundColor('transparent');
        }

        // График 2: доля 3-х популярных браузеров
        $browsersChart = new Chart;
        if (!empty($graphData['browsers'])) {
            $browsersChart->labels(array_values($graphData['dates']));
            foreach ($graphData['browsers'] as $browser => $series) {
                $browsersChart->dataset($browser, 'line', $series)
                    ->backgroundColor('transparent');
            }
        }
//        dd($graphData);// для отладки

        return view('welcome', compact('logs', 'requestsChart', 'browsersChart'));
    }
}

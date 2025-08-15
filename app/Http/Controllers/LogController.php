<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use ConsoleTVs\Charts\Classes\Chartjs\Chart;

use App\Contracts\LogAnalyticsServiceInterface;
use App\Contracts\LogParserServiceInterface;


class LogController extends Controller
{
    public function __construct(
        private LogAnalyticsServiceInterface $logAnalyticsService,
        private LogParserServiceInterface $logParserService
    ) {}

    /**
     * для парсинга одной строки лога
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

        // Данные таблицы
        $logs = $this->logAnalyticsService->getLogsFiltered($filters);
        $graphData = $this->logAnalyticsService->getGraphData($filters);

        // Графики
        $requestsChart = new Chart;
        if (!empty($graphData['dates'])) {
            $requestsChart->labels($graphData['dates']);
            $requestsChart
                ->dataset('Запросы', 'line', $graphData['requests']);
        }

        $browsersChart = new Chart;
        if (!empty($graphData['browsers'])) {
            $browsersChart->labels($graphData['dates']);
            foreach ($graphData['browsers'] as $browser => $series) {
                $browsersChart
                    ->dataset($browser, 'line', $series);
            }
        }
//        dd($graphData);// для отладки
        return view('welcome', compact('logs', 'requestsChart', 'browsersChart'));
    }
}

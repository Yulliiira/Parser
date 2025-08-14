<?php

namespace App\Http\Controllers;

use App\Contracts\LogRepositoryInterface;
use Illuminate\Http\Request;
use App\Services\LogParserService;


class LogController extends Controller
{
    public function __construct(private LogRepositoryInterface $logRepository,
    private LogParserService $logParserService)
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

        // Данные из репозитория
        $logs = $this->logRepository->getLogsFiltered($filters);
        $graphData = $this->logRepository->getGraphData($filters);

        return view('welcome', compact('logs', 'graphData'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use App\Http\Resources\LogEntryResource;
use App\Services\LogParserService;


class LogController extends Controller
{
    /**
     * @param Request $request
     * @param LogParserService $parser
     * @return LogEntryResource| JsonResponse
     */
    public function index(Request $request, LogParserService $parser)
    {
        $line = $request->input('line');
        $model = $parser->stringParse($line);

        if (!$model) {
            return response()->json(['error' => 'Invalid log format'], 400);
        }

        return new LogEntryResource($model);
    }
}

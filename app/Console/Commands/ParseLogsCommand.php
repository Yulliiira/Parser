<?php

namespace App\Console\Commands;

use App\Services\LogParserService;
use Illuminate\Console\Command;

class ParseLogsCommand extends Command
{
    /**
     * @var LogParserService
     */
    protected LogParserService $parserService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Парсит лог nginx и сохраняет данные в БД';

    public function __construct(LogParserService $parserService)
    {
        parent::__construct();
        $this->parserService = $parserService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = storage_path('logs/nginx-access.log');

        if (!file_exists($filePath)) {
            $this->error("Файл не найден: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Начинаю парсинг файла: {$filePath}");

        $handle = fopen($filePath, 'r');
        $count = 0;

        while (($line = fgets($handle)) !== false) {
            $line = trim($line);
            $result = $this->parserService->parseAndSave($line);

            if (!$result) {
                $this->warn("Не удалось распарсить: {$line}");
            } else {
                $this->info("OK: {$line}");
                $count++;
            }
        }

        fclose($handle);

        $this->info("Парсинг завершён. Сохранено записей: {$count}");

        return Command::SUCCESS;
    }
}

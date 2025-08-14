<?php

namespace App\Services;

use App\Services\LogParser\Format;
use App\Services\LogParser\Pattern;
use DateTime;

use App\Contracts\LogParserServiceInterface;
use App\Contracts\LogRepositoryInterface;
use App\DTO\LogEntryDTO;
use App\Models\LogEntry;

class LogParserService implements LogParserServiceInterface
{
    public function __construct(
        private LogRepositoryInterface $repository,
        private Format $format,
        private Pattern $pattern
    ) {}

    public function stringParse(string $parseLine): ?LogEntryDTO
    {
        try {
            $parseLine = trim($parseLine);
            $pattern = $this->pattern->getPattern();
            $identifiers = $this->format->getIdentifiers();

            preg_match($pattern, $parseLine, $matches);
            array_shift($matches);

            // Парсим дату из логов
            $data = array_combine($identifiers, $matches);
            $dt = DateTime::createFromFormat('d/M/Y:H:i:s O', $data['request_date']);

            $data['request_date'] = $dt->format('Y-m-d H:i:s');

            //Нормализация User-Agent
            $uaNormalized = strtolower($data['user_agent']);
            $uaNormalized = preg_replace('/\s+/', ' ', $uaNormalized); // схлопываем пробелы
            $uaNormalized = str_replace(['edg/', 'opr/'], ['edge/', 'opera/'], $uaNormalized); // заменяем алиасы

            // Определяем ОС и архитектуру
            $data['os'] = 'Unknown';
            if (str_contains($uaNormalized, 'windows')) {
                $data['os'] = 'Windows';
            } elseif (str_contains($uaNormalized, 'linux')) {
                $data['os'] = 'Linux';
            } elseif (str_contains($uaNormalized, 'mac os') || str_contains($uaNormalized, 'macintosh')) {
                $data['os'] = 'MacOS';
            }

            $data['architecture'] = 'Unknown';
            if (str_contains($uaNormalized, 'x86_64') || str_contains($uaNormalized, 'win64')) {
                $data['architecture'] = 'x64';
            } elseif (preg_match('/\bx86\b/i', $uaNormalized)) {
                $data['architecture'] = 'x86';
            }

            // Определяем браузер
            $data['browser'] = 'Unknown';
            if (str_contains($uaNormalized, 'edge/')) {
                $data['browser'] = 'Edge';
            } elseif (str_contains($uaNormalized, 'opera/')) {
                $data['browser'] = 'Opera';
            } elseif (str_contains($uaNormalized, 'chrome') && !str_contains($uaNormalized, 'chromium')) {
                $data['browser'] = 'Chrome';
            } elseif (str_contains($uaNormalized, 'safari') && !str_contains($uaNormalized, 'chrome')) {
                $data['browser'] = 'Safari';
            }

            return new LogEntryDTO(
                ip_address: $data['ip_address'],
                request_date: $data['request_date'],
                url: $data['url'],
                os: $data['os'],
                architecture: $data['architecture'],
                browser: $data['browser'],
                user_agent: $data['user_agent']
            );

        } catch (\Throwable $e) {
            error_log("[LogParser] Ошибка парсинга: " . $e->getMessage());// Логируем
            return null;
        }
    }

    public function parseAndSave(string $line): ?LogEntryDTO
    {
        $dto = $this->stringParse($line);
        if (!$dto) {
            return null;
        }

        $this->repository->postLogs($dto);
        return $dto;
    }
}

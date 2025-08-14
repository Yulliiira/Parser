<?php

namespace App\Services;

use DateTime;

use App\Contracts\LogParserServiceInterface;
use App\Contracts\LogRepositoryInterface;
use App\DTO\LogEntryDTO;

class LogParserService implements LogParserServiceInterface
{
    /**
     * @param LogRepositoryInterface $repository
     */
    public function __construct(
        private LogRepositoryInterface $repository,
    )
    {
    }

    /**
     * @param string $parseLine
     * @return LogEntryDTO|null
     */
    public function stringParse(string $parseLine): ?LogEntryDTO
    {
        $regularExpression = '/^([\w\.\:]+) - - \[([^\]]+)\] "\S+ ([^"]+)" \d+ \d+ "[^"]*" "([^"]+)"$/';

        try {
            if (!preg_match($regularExpression, $parseLine, $m)) {
                return null;
            }

            $ip = $m[1];

            // Парсим дату из логов
            $dt = DateTime::createFromFormat('d/M/Y:H:i:s O', $m[2]);
            if (!$dt) {
                return null;
            }
            $date = $dt->format('Y-m-d H:i:s');
            $url = $m[3];
            $ua = trim($m[4]);

            //Нормализация User-Agent
            $uaNormalized = strtolower($ua);
            $uaNormalized = preg_replace('/\s+/', ' ', $uaNormalized); // схлопываем пробелы
            $uaNormalized = str_replace(['edg/', 'opr/'], ['edge/', 'opera/'], $uaNormalized); // заменяем алиасы

            // Определяем ОС и архитектуру
            $os = 'Unknown';
            if (str_contains($uaNormalized, 'windows')) {
                $os = 'Windows';
            } elseif (str_contains($uaNormalized, 'linux')) {
                $os = 'Linux';
            } elseif (str_contains($uaNormalized, 'mac os') || str_contains($uaNormalized, 'macintosh')) {
                $os = 'MacOS';
            }

            $arch = 'Unknown';
            if (str_contains($uaNormalized, 'x86_64') || str_contains($uaNormalized, 'win64')) {
                $arch = 'x64';
            } elseif (preg_match('/\bx86\b/i', $uaNormalized)) {
                $arch = 'x86';
            }

            // Определяем браузер
            $browser = 'Unknown';
            if (str_contains($uaNormalized, 'edge/')) {
                $browser = 'Edge';
            } elseif (str_contains($uaNormalized, 'opera/')) {
                $browser = 'Opera';
            } elseif (str_contains($uaNormalized, 'chrome') && !str_contains($uaNormalized, 'chromium')) {
                $browser = 'Chrome';
            } elseif (str_contains($uaNormalized, 'safari') && !str_contains($uaNormalized, 'chrome')) {
                $browser = 'Safari';
            }

            $dto = new LogEntryDTO(
                ip_address: $ip,
                request_date: $date,
                url: $url,
                os: $os,
                architecture: $arch,
                browser: $browser,
                user_agent: $ua
            );

            return $this->repository->postLogs($dto);
        } catch (\Throwable $e) {
            // Логируем, но не прерываем выполнение сервиса
            error_log("[LogParser] Ошибка парсинга: " . $e->getMessage());
            return null;
        }
    }
}

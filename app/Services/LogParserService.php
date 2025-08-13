<?php

namespace App\Services;

use App\DTO\LogEntryDTO;
use DateTime;

class LogParserService
{
    public function stringParse(string $parseLine): ?LogEntryDTO
    {
        $regulaExpression = '/^(\S+) - - \[(.*?)\] "\S+ (\S+) \S+" \d+ \d+ ".*?" "(.*?)"$/';

        if (preg_match($regulaExpression, $parseLine, $m)) {
            $id = $m[0];
            $ip = $m[1];
            $date = DateTime::createFromFormat('d/M/Y:H:i:s O', $m[2])->format('Y-m-d H:i:s');
            $url = $m[3];
            $ua = $m[4];

            $os = 'Unknown';
            if (stripos($ua, 'Windows') !== false) {
                $os = 'Windows';
            } elseif (stripos($ua, 'Linux') !== false) {
                $os = 'Linux';
            } elseif (stripos($ua, 'Mac OS') !== false){
                $os = 'MacOS';
            }

            $arch = 'Unknown';
            if (stripos($ua, 'x86_64') !== false || stripos($ua, 'Win64') !== false){
                $arch = 'x64';
            } elseif (stripos($ua, 'x86') !== false) {
                $arch = 'x86';
            }

            $browser = 'Unknown';
            if (stripos($ua, 'Chrome') !== false){
                $browser = 'Chrome';
            } elseif (stripos($ua, 'Safari') !== false) {
                $browser = 'Safari';
            }

            return new LogEntryDTO(
                id: $id,
                ip_address: $ip,
                request_date: $date,
                url: $url,
                os: $os,
                architecture: $arch,
                browser: $browser,
                user_agent: $ua
            );
        }
        return null;
    }
}

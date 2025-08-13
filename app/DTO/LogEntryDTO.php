<?php

namespace App\DTO;

class LogEntryDTO
{
    public function __construct(
        public string $id,
        public string $ip_address,
        public int $request_date,
        public string $url,
        public string $os,
        public  string $architecture,
        public string $browser,
        public string $user_agent,

    ){}
}
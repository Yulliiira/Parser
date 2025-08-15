<?php

namespace App\DTO;

class LogDTO
{
    /**
     * @param string $ip_address
     * @param string $request_date
     * @param string $url
     * @param string $os
     * @param string $architecture
     * @param string $browser
     * @param string $user_agent
     */
    public function __construct(
        public readonly string $ip_address,
        public readonly string $request_date,
        public readonly string $url,
        public readonly string $os,
        public readonly string $architecture,
        public readonly string $browser,
        public readonly string $user_agent,
    )
    {
    }
}

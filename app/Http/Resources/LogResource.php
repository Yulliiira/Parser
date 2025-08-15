<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\Log;

class LogResource extends JsonResource
{
    public function toArray(Request $request): mixed
    {
        return [
            'ip_address' => data_get($this->resource, 'ip_address'),
            'request_date' => data_get($this->resource, 'request_date'),
            'url' => data_get($this->resource, 'url'),
            'os' => data_get($this->resource, 'os'),
            'architecture' => data_get($this->resource, 'architecture'),
            'browser' => data_get($this->resource, 'browser'),
            'user_agent' => data_get($this->resource, 'user_agent'),
        ];
    }
}

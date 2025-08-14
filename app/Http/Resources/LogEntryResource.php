<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

use App\Models\LogEntry;

class LogEntryResource extends JsonResource
{
    /**
     * @mixin LogEntry
     */
    public function toArray(Request $request): mixed
    {
        return [
            'ip_address' => $this->resource->ip_address,
            'request_date' => $this->resource->request_date,
            'url' => $this->resource->url,
            'os' => $this->resource->os,
            'architecture' => $this->resource->architecture,
            'browser' => $this->resource->browser,
            'user_agent' => $this->resource->user_agent,
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->resource->id ?? '',
            'title' => $this->resource->title,
            'content' => $this->resource->content,
            'date' => $this->resource->date,
            'source' => $this->resource->source,
            'user_id' => optional($this->resource->user)->id,
            'user_name' => optional($this->resource->user)->name,
        ];

        return $data;
    }
}

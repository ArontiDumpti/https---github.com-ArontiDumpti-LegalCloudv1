<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProcessingJobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'progress' => $this->progress,
            'resultText' => $this->result_text,
            'errorCode' => $this->error_code,
            'createdAt' => $this->created_at,
            'completedAt' => $this->completed_at,
        ];
    }
}

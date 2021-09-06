<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectMoneyListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            "id"            => $this['id'],
            "phase_id"      => $this['phase_id'],
            "title"         => $this['title'],
            "description"   => $this['description'],
            "amount"        => $this['amount'],
            "type"          => $this['type'],
            "created_at"    => $this['created_at'],
            "updated_at"    => $this['updated_at']
        ];
    }
}

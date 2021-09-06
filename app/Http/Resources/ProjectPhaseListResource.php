<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectPhaseListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            "id"            => $this['id'],
            "project_id"    => $this['project_id'],
            "title"         => $this['title'],
            "description"   => $this['description'],
            "amount"        => $this['amount'],
            "status"        => $this['status'],
            "created_at"    => $this['created_at'],
            "updated_at"    => $this['updated_at']
        ];
    }
}

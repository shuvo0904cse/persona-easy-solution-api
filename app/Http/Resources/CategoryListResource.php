<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            "id"            => $this['id'],
            "title"         => $this['title'],
            "icon"          => $this['icon'],
            "type"          => $this['type'],
            "created_at"    => $this['created_at'],
            "updated_at"    => $this['updated_at']
        ];
    }
}

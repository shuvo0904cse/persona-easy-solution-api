<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NoteListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            "id"            => $this['id'],
            "title"         => $this['title'],
            "description"   => $this['description'],
            "status"        => $this['status'],
            "created_at"    => $this['created_at'],
            "updated_at"    => $this['updated_at']
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroceryListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {   
        $array = [
            "id"            => $this['id'],
            "title"         => $this['title']
        ];
        if(isset($this['pivot'])){
            $newArray = [
                "amount"        => $this['pivot']['amount'],
                "unit"          => $this['pivot']['unit']
            ];
            $array = array_merge($array, $newArray);
        }
        return $array;
    }
}

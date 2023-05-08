<?php

namespace App\ExportResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class CountResourceCollection extends AnonymousResourceCollection
{
    /**
     * Transform the resource into a JSON array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    #[\ReturnTypeWillChange]
    public function toArray(Request $request)
    {
        return [
            'count' => $this->count(),
            'type' => Str::of($this->collects)->afterLast('\\')->kebab()->replace('-', '_'),
            'values' => parent::toArray($request),
        ];
    }
}

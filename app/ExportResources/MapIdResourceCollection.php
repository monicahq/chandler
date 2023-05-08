<?php

namespace App\ExportResources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class MapIdResourceCollection extends AnonymousResourceCollection
{
    /**
     * Transform the resource into a JSON array.
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    #[\ReturnTypeWillChange]
    public function toArray(Request $request)
    {
        $class = Str::of($this->collects)->afterLast('\\');
        $id = (new("\\App\\Models\\$class"))->usesUniqueIds ? 'id' : 'uuid';

        return [
            'count' => $this->count(),
            'type' => $class->kebab()->replace('-', '_'),
            'values' => $this->collection->pluck($id),
        ];
    }
}

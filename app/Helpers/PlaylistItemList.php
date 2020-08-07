<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class PlaylistItemList
{
    public Collection $items;
    public string $nextToken = '';

    /**
     * PlaylistItemList constructor.
     * @param Collection $items
     * @param string $nextToken
     */
    public function __construct(Collection $items, string $nextToken)
    {
        $this->items = $items;
        $this->nextToken = $nextToken;
    }
}

<?php

namespace App\Helpers;

class PlaylistItem
{
    public string $title;
    public string $id;

    /**
     * PlaylistItem constructor.
     * @param string $title
     * @param string $id
     */
    public function __construct(string $title, string $id)
    {
        $this->title = $title;
        $this->id = $id;
    }
}

<?php

namespace Tests\Feature;

use App\Arc;
use App\Episode;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ArcTest extends TestCase
{
    use DatabaseMigrations;

    public function test_home_shows_all_arcs(): void
    {
        $arcs = factory(Arc::class, 3)->create();

        $this->get('/')
            ->assertOk()
            ->assertPropCount('arcs', $arcs->count());
    }

    public function test_arc_shows_all_episodes(): void
    {
        $arc = factory(Arc::class)->create();
        $episodes = factory(Episode::class, 3)->create([
            'arc_id' => $arc->id,
        ]);

        $this->get("/arc/{$arc->id}")
            ->assertOk()
            ->assertPropCount('arc.episodes', $episodes->count());
    }
}

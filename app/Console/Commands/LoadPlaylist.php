<?php

namespace App\Console\Commands;

use App\Arc;
use App\Episode;
use App\Helpers\PlaylistItem;
use App\Helpers\PlaylistItemList;
use Google_Client;
use Google_Service_YouTube;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class LoadPlaylist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ltv:playlist {--r|reverse} {playlist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load a YouTube playlist into the database.';

    /**
     * Google API instance.
     */
    protected Google_Service_YouTube $youtube;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $playlist = $this->argument('playlist');
        if (! $playlist) {
            $this->error('No playlist specified.');
        }

        $client = new Google_Client();
        $client->setDeveloperKey(config('services.youtube.key'));
        $this->youtube = new Google_Service_YouTube($client);

        $this->info('Fetching playlist...');

        $nextToken = '';
        $items = new Collection();
        $complete = false;
        while (! $complete) {
            try {
                $list = $this->fetchPlaylist($playlist, $nextToken);
            } catch (\Exception $e) {
                $this->error('An error occurred in fetching the playlist, aborting.');
                return 1;
            }

            $items = $items->merge($list->items);

            $nextToken = $list->nextToken;
            if (! $nextToken) {
                $complete = true;
            }
        }

        if ($this->option('reverse')) {
            $items = $items->reverse();
        }

        $this->save($items);
        return 0;
    }

    /**
     * Fetch the playlist with the given token.
     *
     * @param string $playlist
     * @param string $nextToken
     * @return PlaylistItemList
     */
    protected function fetchPlaylist(string $playlist, string $nextToken = ''): PlaylistItemList
    {
        $response = $this->youtube->playlistItems->listPlaylistItems('snippet', [
            'playlistId' => $playlist,
            'maxResults' => 50,
            'pageToken' => $nextToken,
        ]);

        return new PlaylistItemList(
            Collection::make($response->items)
                ->map(fn ($v) => new PlaylistItem(
                    $v->snippet->title,
                    $v->snippet->resourceId->videoId,
                )),
            $response->nextPageToken ?? '',
        );
    }

    /**
     * Save the playlist to the database.
     *
     * @param Collection $videos
     */
    protected function save(Collection $videos): void
    {
        $this->info("Processing {$videos->count()} episodes...");

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();

        $videos->each(function (PlaylistItem $video) use ($bar) {
            // i really need to learn regex
            $episodeId = (int) substr(explode(' ', $video->title)[1], 1);
            $arc = $this->fetchArc($episodeId);

            Episode::firstOrCreate([
                'title' => "#{$episodeId}",
                'videoId' => $video->id,
                'arc_id' => $arc->id,
            ]);

            $bar->advance();
        });

        $bar->finish();
    }

    /**
     * Fetch or create an arc depending on the episode ID.
     *
     * @param int $episode
     * @return Arc
     */
    protected function fetchArc(int $episode): Arc
    {
        $arc = null;
        foreach (config('episodes.mapping') as $name=>$arc) {
            $checkSingle = fn ($episode, $arc) => $episode >= $arc[0] && $episode <= $arc[1];
            $checkArray = static function ($episode, $arc) use ($checkSingle) {
                return in_array(true, array_map(fn($arr) => $checkSingle($episode, $arr), $arc), true);
            };

            if ((is_array($arc[0]) && $checkArray($episode, $arc)) || $checkSingle($episode, $arc)) {
                $arc = $name;
                break;
            }
        }

        return Arc::firstOrCreate([
            'name' => $arc,
        ]);
    }
}

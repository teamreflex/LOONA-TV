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
    protected $signature = 'ltv:playlist {--r|reverse} {--c|--create} {--a|--add} {playlist}';

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

        if ($this->option('create')) {
            $this->info('Creating a new arc...');
            $arc = Arc::create([
                'name' => $this->ask('Arc name?', ''),
                'color' => $this->ask('Arc color?'),
                'order' => (int) $this->ask('Arc order?'),
            ]);
        }

        if ($this->option('add')) {
            $this->info('Adding playlist into separate arcs...');

            $availableArcs = Arc::all();
            $items->each(function (PlaylistItem $video, int $index) use ($availableArcs) {
                $this->info("Processing video: {$video->title}");
                $name = $this->choice('Select an arc to add to', $availableArcs->pluck('name')->all());

                $this->info($name);
                $arc = $availableArcs->firstWhere('name', $name);
                if (! $arc) {
                    $this->error('Invalid arc.');
                    return 1;
                }

                Episode::firstOrCreate([
                    'title' => $video->title,
                    'videoId' => $video->id,
                    'arc_id' => $arc->id,
                ]);

                $this->info("Processed: {$video->title}, moving on...");
            });

            return 0;
        }

        $this->save($items, $arc ?? null);
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
     * @param Arc|null $arc
     */
    protected function save(Collection $videos, ?Arc $arc = null): void
    {
        $this->info("Processing {$videos->count()} episodes...");

        $bar = $this->output->createProgressBar($videos->count());
        $bar->start();

        $videos->each(function (PlaylistItem $video, int $index) use ($bar, $arc) {
            $episodeId = $this->parseEpisodeId($video->title);
            $arc ??= $this->fetchArc($episodeId);

            Episode::firstOrCreate([
                'title' => $episodeId ? "#{$episodeId}" : '#' . ($index + 1),
                'videoId' => $video->id,
                'arc_id' => $arc->id,
            ]);

            $bar->advance();
        });

        $bar->finish();
    }

    /**
     * I really need to learn regex
     *
     * @param string $title
     * @return int
     */
    protected function parseEpisodeId(string $title): int
    {
        return (int) substr(explode(' ', $title)[1], 1);
    }

    /**
     * Fetch or create an arc depending on the episode ID.
     *
     * @param int $episode
     * @return Arc
     */
    protected function fetchArc(int $episode): Arc
    {
        $arcName = null;
        $currentArc = null;
        foreach (config('episodes.mapping') as $name=>$arc) {
            $checkSingle = fn ($episode, $arc) => $episode >= $arc[0] && $episode <= $arc[1];
            $checkArray = static function ($episode, $arc) use ($checkSingle) {
                return in_array(true, array_map(fn($arr) => $checkSingle($episode, $arr), $arc), true);
            };

            if ((is_array($arc['episodes'][0]) && $checkArray($episode, $arc['episodes'])) || $checkSingle($episode, $arc['episodes'])) {
                $arcName = $name;
                $currentArc = $arc;
                break;
            }
        }

        return Arc::firstOrCreate([
            'name' => $arcName,
            'color' => $currentArc['color'] ?? null,
            'order' => $currentArc['order'] ?? null,
        ]);
    }
}

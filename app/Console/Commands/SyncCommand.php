<?php

namespace App\Console\Commands;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Cover;
use App\Models\Genre;
use App\Models\Setting;
use App\Models\Year;
use App\Services\FileService;
use getID3;
use Illuminate\Console\Command;
use App\Models\Song;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $path = Setting::get('media_path');
        if (is_null($path)) {
            echo "Введите путь к папке с файлами: ";
            $inputPath = fgets(STDIN);
            $setting = new Setting();
            $setting->key = 'media_path';
            $setting->value = $inputPath;
            $setting->save();

            $path = Setting::get('media_path');
        }

        $this->info('Syncing media from ' . $path . PHP_EOL);

        $files = FileService::getDirFiles($path);

        $files = array_filter($files, function ($v, $k) {
            return substr($v, -3) == 'mp3';
        }, ARRAY_FILTER_USE_BOTH);

        $songs = Song::pluck('file_name')->all();

        foreach ($files as $key => $file) {
            $array = explode('/', $file);
            $searchSong = array_search(end($array), $songs);
            if ($searchSong !== false) {
                unset($songs[$searchSong]);
                unset($files[$key]);
            }
        }

        $countTracks = count($files);
        //Добавить удаление из БД

        foreach ($songs as $song) {
            Song::deleteSong($song);
        }

        foreach ($files as $file) {
            $getID3 = new getID3;
            $info = $getID3->analyze($file);

            if (isset($info['error']) || !isset($info['playtime_seconds'])) {
                $this->syncError = isset($info['error']) ? $info['error'][0] : 'No playtime found';

                return [];
            }
            $getID3->CopyTagsToComments($info);

            $image = '';
            if (array_key_exists('comments', $info)) {
                $image = 'data:' . $info['comments']['picture'][0]['image_mime'] . ';charset=utf-8;base64,' . base64_encode($info['comments']['picture'][0]['data']);
            }

            // Подумать над сохранением для одинаковых по структуре таблиц
            $song = new Song();
            $song->save();

            if (!empty($info['tags']['id3v2']['album'])) {
                $song->album_id = Album::getAlbum($info['tags']['id3v2']['album'][0]);
            }
            if (!empty($info['tags']['id3v2']['artist'])) {
                $song->artist_id = Artist::getArtist($info['tags']['id3v2']['artist'][0]);
            }
            if (!empty($info['tags']['id3v2']['year'])) {
                $song->year_id = Year::getYear($info['tags']['id3v2']['year'][0]);
            }
            if ($image) {
                $song->cover_id = Cover::getCover($image);
            }
            if (!empty($info['tags']['id3v2']['title'])) {
                $song->title = $info['tags']['id3v2']['title'][0];
            }
            $song->length = $info['playtime_seconds'];
            //Подумать над очисткой пути
            $song->path = str_replace($path, '', $info['filepath'] . '/');
            $song->file_name = $info['filename'];
            if (!empty($info['tags']['id3v2']['genre'])) {
                Genre::addGenre($song->id, $info['tags']['id3v2']['genre'][0]);
            }

            $song->save();

            echo $countTracks . ' ' . $file . PHP_EOL;
            $countTracks--;
        }

        return 0;
    }
}

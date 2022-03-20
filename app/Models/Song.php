<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    public static function deleteSong($data)
    {
        $song = Song::where('file_name', $data)->first();
        if (Song::where('album_id', $song->album_id)->count() == 1 && !is_null($song->album_id)) {
            Album::where('id', $song->album_id)->delete();
        }
        if (Song::where('artist_id', $song->artist_id)->count() == 1 && !is_null($song->artist_id)) {
            Artist::where('id', $song->artist_id)->delete();
        }
        if (Song::where('year_id', $song->year_id)->count() == 1 && !is_null($song->year_id)) {
            Year::where('id', $song->year_id)->delete();
        }
        if (Song::where('cover_id', $song->cover_id)->count() == 1 && !is_null($song->cover_id)) {
            $cover = Cover::where('id', $song->cover_id)->first();
            if (file_exists(base_path() . '/public' . $cover->cover_path . $cover->cover_hash . '.jpg')) {
                unlink(base_path() . '/public' . $cover->cover_path . $cover->cover_hash . '.jpg');
            }
            $cover->delete();
        }
        songGenre::where('song_id', $song->id)->delete();
        $song->delete();
        echo 'deleted: ' . $data . PHP_EOL;
    }
}

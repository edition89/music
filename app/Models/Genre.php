<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use HasFactory;

    public static function addGenre($songId, $genres)
    {
        if (!$genres) {
            return;
        }
        foreach (explode('/', $genres) as $genreTitle) {
            $genre = Genre::where('name', $genreTitle)->first();
            if (is_null($genre)) {
                $genre = new Genre();
                $genre->name = $genreTitle;
                $genre->save();
            }

            $songGenre = new songGenre();
            $songGenre->song_id = $songId;
            $songGenre->genre_id = $genre->id;
            $songGenre->save();

        }
    }
}

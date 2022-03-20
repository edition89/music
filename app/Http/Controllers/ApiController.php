<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Artist;
use App\Models\Cover;
use App\Models\Genre;
use App\Models\Setting;
use App\Models\Song;
use App\Models\songGenre;
use App\Models\Year;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getSongList(Request $request)
    {
        $offsetSong = $request->input('offsetSong');

        $songs = Song::offset(30 * $offsetSong)->limit(30)->get();

        $data = array();
        $pathSongs = str_replace('\\', '/', Setting::get('media_path'));
        foreach ($songs as $song) {
            $cover = Cover::where('id', $song->cover_id)->first();
            $coverPath = $cover->cover_path . $cover->cover_hash . '.jpg';
            $album = Album::where('id', $song->album_id)->value('name');
            $genreId = songGenre::where('song_id', $song->id)->first();
            $songData = array(
                'id' => $song->id,
                'artist' => Artist::where('id', $song->artist_id)->value('name'),
                'title' => $song->title,
                'album' => $album ? $album : 'Неизвестный альбом',
                'year' => Year::where('id', $song->year_id)->value('name'),
                'length' => $song->length,
                'path' => str_replace($pathSongs, '', $song->path) . 'music/',
                'file_name' => $song->file_name,
                'play_count' => $song->play_count,
                'cover_path' => file_exists(base_path() . '/public' . $coverPath) ? $coverPath : '/image/cover.png',
                'genre' => $genreId->genre_id == null ? '' : Genre::where('id', $genreId->genre_id)->value('name'),
            );
            array_push($data, $songData);
        }

        return response()->json(array(
            'success' => true,
            'data' => $data
        ));
    }

    public function playCount(Request $request) {
        $id = $request->input('id');
        $song = Song::where('id' , $id)->first();
        $song->play_count += 1;
        $song->save();

        return response()->json(array(
            'success' => true,
            'message' => 'success'
        ));
    }

    public function getTableParameters(){
        return response()->json(array(
            'success' => true,
            'data' => json_decode(Setting::get('table_parameters'))
        ));
    }
}

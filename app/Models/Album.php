<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    public static function getAlbum($data){

        $album = Album::where('name', $data)->first();

        if(is_null($album)){
            return Album::addAlbum($data);
        }

        return $album->id;
    }

    public function addAlbum($data){

        $album = new Album();
        $album->name = $data;
        $album->save();

        return $album->id;
    }
}

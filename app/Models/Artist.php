<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    use HasFactory;

    public static function getArtist($data){

        $artist = Artist::where('name', $data)->first();

        if(is_null($artist)){
            return Artist::addArtist($data);
        }

        return $artist->id;
    }

    public function addArtist($data){

        $artist = new Artist();
        $artist->name = $data;
        $artist->save();

        return $artist->id;
    }
}

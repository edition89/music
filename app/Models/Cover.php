<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cover extends Model
{
    use HasFactory;

    public static function getCover($data)
    {
        $coverHash = hash("md5", $data);

        $cover = Cover::where('cover_hash', $coverHash)->first();

        if (is_null($cover)) {
            return Cover::addCover($coverHash, $data);
        }

        return $cover->id;
    }

    public function addCover($coverHash, $data)
    {
        $path = '/image/covers/' . substr(md5(time()), 0, 2) . '/';
        $folder = base_path() . '/public' . $path;
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        $ifp = fopen($folder . $coverHash . '.jpg', 'wb');
        $data = explode(',', $data);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        $cover = new Cover();
        $cover->cover_path = $path;
        $cover->cover_hash = $coverHash;
        $cover->save();

        return $cover->id;
    }
}

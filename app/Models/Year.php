<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    public static function getYear($data){

        $year = Year::where('name', $data)->first();

        if(is_null($year)){
            return Year::addYear($data);
        }

        return $year->id;
    }

    public function addYear($data){

        $year = new Year();
        $year->name = $data;
        $year->save();

        return $year->id;
    }
}

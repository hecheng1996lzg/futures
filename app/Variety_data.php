<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variety_data extends Model
{
    protected $table = 'variety_data' ;

    public function variety(){
        return $this->belongsTo('App\Variety');
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Selection extends Model
{

    public function variety(){
        return $this->belongsTo('App\Variety');
    }

}

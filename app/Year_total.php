<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year_total extends Model
{
    protected $table = 'year_totals' ;

    public function year(){
        return $this->belongsTo('App\Year');
    }

}

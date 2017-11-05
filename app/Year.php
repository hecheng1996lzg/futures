<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    public function variety(){
        return $this->belongsTo('App\Variety');
    }

    public function term()
    {
        return $this->belongsToMany('App\Trading_term', 'year_totals', 'year_id', 'term_id');
    }

    public function trading_records(){
        return $this->hasMany('App\Trading_record');
    }

}

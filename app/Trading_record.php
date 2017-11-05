<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trading_record extends Model
{
    protected $table = 'trading_records' ;

    public function trading_term(){
        return $this->belongsTo('App\Trading_term');
    }

    public function year(){
        return $this->belongsTo('App\Year');
    }

    public function trading_last()
    {
        return $this->hasOne('App\Trading_last','record_id');
    }


}

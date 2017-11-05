<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trading_last extends Model
{
    protected $table = 'trading_last' ;

    public function trading_record()
    {
        return $this->belongsTo('App\Trading_record','record_id');
    }

    public function trading_term()
    {
        return $this->hasOne('App\Trading_term','last_id');
    }

}

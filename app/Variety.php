<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variety extends Model
{
    public function getYear($date){
        /*
         * 参数格式 2009-03-27
         * 返回 年份
         * */
        $arr = [];
        preg_match_all('/\d+/',$date,$arr);
        return $arr[0][0];
    }

}

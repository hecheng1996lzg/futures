<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Variety extends Model
{

    public function variety_data(){
        return $this->hasMany('App\Variety_data');
    }

    public function selections(){
        return $this->hasMany('App\Selection');
    }

    public function trading_terms(){
        return $this->hasMany('App\Trading_term');
    }

    public function years(){
        return $this->hasMany('App\Year');
    }


    /**
     * 获取年份
     * 参数格式 2009-03-27
     * 返回 年份
     **/
    public function getYear($date){
        $arr = [];
        preg_match_all('/\d+/',$date,$arr);
        return $arr[0][0];
    }

    /**
     * 截取数据
     * 参数格式
     *      数据源（文本）
     *      输入开始时间
     *      输入结束时间
     * 返回 年份
     **/
    public function cutData($path,$start=0,$end=9999999999){
        $data = file_get_contents($path);
        $data = explode("\r\n",$data);
        $results  = [];

        array_shift($data);
        array_pop($data);

        foreach ($data as $key=>$value){
            $row = explode(" ",$value);
            $row_time = strtotime($row[0]);
            if($row_time >= $start && $row_time <= $end){
                $results[] = $row;
            }
        }

        return $results;
    }

    /**
     * 保存片段日期数据
     **/
    public function saveFragment($data){
        foreach ($data as $row){
            $v_data = new Variety_data();
            $v_data->variety_id = $this->id;
            $v_data->date = $row[0];
            $v_data->closing = $row[1];
            $v_data->opened = $row[2];
            $v_data->highest = $row[3];
            $v_data->minimum = $row[4];
            $v_data->deal = $row[5];
            $v_data->positions = $row[6];
            $v_data->save();
        }
        return true;
    }

}

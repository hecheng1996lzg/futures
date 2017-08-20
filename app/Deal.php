<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    public $initial_capital; //储存计算时每年初始资金
    public $prev_num = 0; //储存计算时每年盈亏的变量
    public $prev_year; //储存每年的年号

    public $total = [];

    public $continuities = []; //连续几日的“均线浮动值”

    public $deal_state; //储存着上一次交易的状态是买入还是卖出
    public $deal_number; //储存着上一次交易的价格

    public $test_num = 0;
    public $test_array = [];

    function __construct($data=null,$continuity=null,$average=null,$multiple=null) {
        $this->data = $data;
        $this->continuity = $continuity;
        $this->average = $average;
        $this->multiple = $multiple;
    }

    public function prevailingThanAverage(){
        foreach ($this->data as $key=>$value){
            $year = $this->getYear($value[0]);
            $bool = ($this->continuity+$this->average-2<=$key)? // 固定均线不能为空值的计算
                $this->isMoreAverage($key): // true:卖 ; false：买
                '--';
            $isCount = $this->setContinuityArr($bool);
            /*
             * 跨年则更新初始资金
             * 跨年度需要总结上一年的总金额
             * */
            if($year != $this->prev_year){
                $this->countYearTotal();
                $this->resetInitialCapital($value[1]);
                $this->prev_year = $year;
                $this->prev_num = 0;
            }

            /*
             * 满足交易条件首先操作结果不能为--
             * */
            if($isCount!=='--'){
                /*
                 * 第一次开始交易 或 与之前交易操作相反
                 * 计算上一次操作和这次操作的差值
                 * */
                if($this->deal_state===null || $bool !== $this->deal_state){
                    $this->calculationTotal($value,$bool);
                }
            }

            /*
             * 最后一个值进行结束计算
             * */
            if($key+1 == count($this->data) && !array_key_exists($year,$this->total)){
                $this->prev_year = $year;
                $this->calculationTotal($value, !$this->deal_state);
                $this->countYearTotal();
            }
        }
        return round(array_sum($this->total)*100,2).'%';
    }

    public function averageThanAverage(){
        foreach ($this->data as $key=>$value){
            $year = $this->getYear($value[0]);
            $bool = ($this->continuity+$this->average-2<=$key)?
                $this->isMoreYesterdayAverage($key):
                '--';
            $isCount = $this->setContinuityArr($bool);
            if($year != $this->prev_year){
                $this->countYearTotal();
                $this->resetInitialCapital($value[1]);
                $this->prev_year = $year;
                $this->prev_num = 0;
            }

            if($isCount!=='--'){
                if($this->deal_state===null || $bool !== $this->deal_state){
                    $this->calculationTotal($value,$bool);
                }
            }

            if($key+1 == count($this->data) && !array_key_exists($year,$this->total)){
                $this->prev_year = $year;
                $this->calculationTotal($value, !$this->deal_state);
                $this->countYearTotal();
            }
        }
        return round(array_sum($this->total)*100,2).'%';
    }

    public function prevailingThanPrevious(){
        foreach ($this->data as $key=>$value){
            $year = $this->getYear($value[0]);
            $bool = ($this->continuity+$this->average-2<=$key)?
                $this->isMoreYesterdayPrice($key):
                '--';
            $isCount = $this->setContinuityArr($bool);
            if($year != $this->prev_year){
                $this->countYearTotal();
                $this->resetInitialCapital($value[1]);
                $this->prev_year = $year;
                $this->prev_num = 0;
            }

            if($isCount!=='--'){
                if($this->deal_state===null || $bool !== $this->deal_state){
                    $this->calculationTotal($value,$bool);
                }
            }

            if($key+1 == count($this->data) && !array_key_exists($year,$this->total)){
                //dd($key,$this->data,count($this->data),$year,$value);
                $this->prev_year = $year;
                $this->calculationTotal($value, !$this->deal_state);
                $this->countYearTotal();
            }
        }
        return round(array_sum($this->total)*100,2).'%';
    }


    private function calculationTotal($value, $bool){
        if($this->deal_number!==null) {
            if($bool){ //买入
                $this->prev_num += $this->deal_number - $value[1];
            }else{ //卖出
                $this->prev_num += $value[1] - $this->deal_number;
            }
            //if($this->prev_year==2015)dd($this->prev_year,$this->deal_number,$value[1]);

        }
        //$this->test_array[] = [$value[0],$value[1],$this->deal_number];

        $this->deal_state = $bool;
        $this->deal_number = $value[1];
    }

    private function countYearTotal(){
        if($this->prev_num==0)return ;
        $num = $this->prev_num * $this->multiple / $this->initial_capital;
        $this->total[$this->prev_year] = $num;
        if($this->prev_year!=2014){
            //dd($this->prev_num,$this->multiple,$this->initial_capital);
        }
    }

    private function resetInitialCapital($price){
        $this->initial_capital =  $price * $this->multiple * 0.25;
        //$this->test_array[] = $this->initial_capital;
    }

    /**
     * 获取当天的均线价格
     */
    private function getAverageAboveOne($key){
        $total = 0;
        $num = 0;
        for ($i=0; $i<$this->average; $i++){
            if(isset($this->data[$key-$i])){
                $total+=$this->data[$key-$i][1];
                $num++;
            }
        }
        return $total/$num;
    }

    /**
     * 当天价格是否超过均线
     * */
    private function isMoreAverage($key){
        $ave = $this->getAverageAboveOne($key);
        return $ave < $this->data[$key][1];
    }

    /**
     * 当天均价是否超过昨日均线
     * */
    private function isMoreYesterdayAverage($key){
        $today = $this->getAverageAboveOne($key);
        $yesterday = $this->getAverageAboveOne($key-1);
        return $yesterday < $today;
    }

    /**
     * 当天价格是否超过昨日价格
     **/
    private function isMoreYesterdayPrice($key){
        return $this->data[$key-1][1] < $this->data[$key][1];
    }

    /**
     * 对比前几日，判断是否达到连续天数。
     * 返回true 或 false 或 --
     * */
    private function setContinuityArr($bool){
        array_push($this->continuities, $bool);
        if(count($this->continuities) > $this->continuity){
            array_shift($this->continuities);
        }
        $uniqueContinuity = array_unique($this->continuities);

        if(count($this->continuities)==$this->continuity && count($uniqueContinuity)==1){
            return $uniqueContinuity[0];
        }

        return '--';
    }

    public function getYear($date){
        /*
         * 参数格式 2009-03-27
         * 返回 年份
         * */
        $arr = [];
        preg_match_all('/\d+/',$date,$arr);
        return $arr[0][0];
    }

    public function prevailingThanAverage_Weight(){
        $num = 1;
        foreach ($this->total as $value){
            $num *= $value+1;
        }
        return round($num,2);
    }

}

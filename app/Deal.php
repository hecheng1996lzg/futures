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

    public $test_num = 0; //调试用
    public $test_array = []; //调试用

    function __construct($data=null,$continuity=null,$average=null,$multiple=null,$comparative_type=1) {
        $this->data = $data;
        $this->continuity = $continuity;
        $this->average = $average;
        $this->multiple = $multiple;
        $this->comparative_type = $comparative_type;
    }

    /**
     * 遍历日期范围内的数据，计算总盈利百分比
     **/
    public function countProfit_Percentage(){
        foreach ($this->data as $key=>$value){
            $year = $this->getYear($value[0]);
            /**
             * 计算当日是否满足条件。
             **/
            if($this->continuity+$this->average-2<=$key){
                /**
                 * 根据比较类型，进行不同计算当天是
                 * true:卖 ; false：买 ; --：无结果
                 */
                switch ($this->comparative_type){
                    case 1: //将当日与前几日均线对比，且连续几天
                        $bool = $this->isMoreAverage($key);
                        break;
                    case 2: //将当日均线与前几日均线对比，且连续几天
                        $bool = $this->isMoreYesterdayAverage($key);
                        break;
                    case 3: //将当日与昨天对比，且连续几天
                        $bool = $this->isMoreYesterdayPrice($key);
                        break;
                }
            }else{
                // 前几日不构成连续几日平均值的条件，直接为--
                $bool = '--';
            }

            $operation = $this->setContinuityArr($bool);

            /**
             * 跨年则更新初始资金
             * 跨年度需要总结上一年的总金额
             **/
            if($year != $this->prev_year){
                $this->countYearTotal();  //总结去年的盈利
                $this->resetInitialCapital($value[1]);  //计算今年的初始资金
                $this->prev_year = $year;
                $this->prev_num = 0;
            }

            /**
             * 满足交易条件首先操作结果不能为--
             **/
            if($operation!=='--'){
                /*
                 * 第一次开始交易 或 与之前交易操作相反
                 * */
                if($this->deal_state===null || $bool !== $this->deal_state){
                    $this->calculationTotal($value,$bool);
                }
            }

            /**
             * 最后一个值进行结束计算
             * 结束计算：以当前价格卖掉或买入手上的商品
             **/
            if($key+1 == count($this->data) && !array_key_exists($year,$this->total)){
                $this->prev_year = $year;
                $this->calculationTotal($value, !$this->deal_state);
                $this->countYearTotal();
            }
        }
        return round(array_sum($this->total)*100,2).'%';
    }

    /**
     * 参数：$value ：一条数据
     *      $bool  ：交易类型
     * 返回值：空
     * 执行：根据交易类型，计算盈亏数值。保存这次交易时的状态和价格。
     **/
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

    /**
     * 计算年份收益百分比
     * 盈亏值 / 初始资金 × 手数 × 100%
     * 将结果保存至 年份盈利数组 中，用于后期统计。
     **/
    private function countYearTotal(){
        if($this->prev_num==0)return ;
        $num = $this->prev_num * $this->multiple / $this->initial_capital;
        $this->total[$this->prev_year] = $num;
    }

    /**
     * 计算年份初始资金
     * 第一天的收盘价 × 手数 × 25%
     **/
    private function resetInitialCapital($price){
        $this->initial_capital =  $price * $this->multiple * 0.25;
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
     * 保存当日操作方式。
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

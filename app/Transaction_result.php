<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction_result extends Model
{
    protected $table = 'transaction_results' ;

    public function transaction_last()
    {
        return $this->hasOne('App\Transaction_last', 'results_id');
    }

    public function transaction_years()
    {
        return $this->hasMany('App\Transaction_year', 'results_id');
    }

    public $initial_capital; //储存计算时每年初始资金
    public $prev_num = 0; //储存计算时每年盈亏的变量
    public $prev_year; //储存每年的年号

    public $total = [];

    public $continuities = []; //连续几日的“均线浮动值”

    public $deal_state; //储存着上一次交易的状态是买入还是卖出
    public $deal_number; //储存着上一次交易的价格

    public $transaction_last_arr = []; // 年份，类型，值
    public $variety_year = []; //年份数据

    public $test_num = 0; //调试用
    public $test_array = []; //调试用

    public function saveRecord($data=null,$continuity=null,$average=null,$multiple=null,$comparative_type=1, $variety_id) {
        $this->continuity = $continuity;
        $this->average = $average;
        $this->variety_id = $variety_id;
        $this->save();

        $this->data = $data;
        $this->multiple = $multiple;
        $this->comparative_type = $comparative_type;
        $t_last = $this->transaction_last? $this->transaction_last->datetime:0;
        $this->start_update($t_last);
    }

    /**
     * 遍历日期范围内的数据，计算总盈利百分比
     **/
    public function start_update($t_last){
        foreach ($this->data as $key=>$value){
            /**
             * 之前的记录不计算
             **/
            $dateTime = strtotime($value[0]);
            if($t_last>=$dateTime) continue;

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
                $this->resetInitialCapital($value[1],$year);  //计算今年的初始资金
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
                    $this->transaction_last_arr[0] = $dateTime;
                    $this->transaction_last_arr[1] = $bool;
                    $this->transaction_last_arr[2] = $value[1];
                }
            }

            /**
             * 最后一个值进行结束计算 && 并且没有年度清算，不存在total中 &&
             * 结束计算：以当前价格卖掉或买入手上的商品
             **/
            if($key+1 == count($this->data) && !array_key_exists($year,$this->total)){
                $this->prev_year = $year;

                /* 没有进行正常交易 */
                if($this->transaction_last_arr[0]!=$dateTime){
                    $this->calculationTotal($value, !$this->deal_state);
                }
                $this->countYearTotal();
            }
        }

        /* 产品对应年份初始数据只需要保存一次 */
        $variety_year_data = Year::where('variety_id',$this->variety_id)->count();
        if(!$variety_year_data){
            $this->setVariety_year($this->variety_year);
        }
        $this->setVariety_year_id($this->variety_year);

        $this->setTransaction_year($this->total);
        $this->setTransaction_last($this->transaction_last_arr);

    }

    /**
     * 将$variety_year 值 转为 对应id
     **/
    private function setVariety_year_id($data){
        foreach ($data as $key => $value) {
            $year = Year::where([
                'variety_id'=>$this->variety_id,
                'year'=>$key,
            ])->first();
            $this->variety_year[$key] = $year->id;
        }
    }
    /**
     * 写入各年份初始资金
     **/
    private function setVariety_year($data){
        foreach ($data as $key=>$value){
            $year = new Year();
            $year->variety_id = $this->variety_id;
            $year->year = $key;
            $year->initial_capital = $value;
            $year->save();
        }
    }

    /**
     * 写入各年份数据
     **/
    private function setTransaction_year($data){
        foreach ($data as $key=>$value){
            $transaction_year = Transaction_year::where([
                    'results_id'=>$this->id,
                    'year_id'=>$this->variety_year[$key]
                ])->first();
            $transaction_year = $transaction_year? $transaction_year:new Transaction_year();
            $transaction_year->results_id = $this->id;
            $transaction_year->year_id = $this->variety_year[$key];
            $transaction_year->value = $value;
            $transaction_year->save();
        }
    }

    /**
     * 写入最后一条记录
     **/
    private function setTransaction_last($data){
        $transaction_last = $this->transaction_last? $this->transaction_last:new Transaction_last();
        $transaction_last->results_id = $this->id;
        $transaction_last->datetime = $data[0];
        $transaction_last->type = $data[1]? 1:2;
        $transaction_last->value = $data[2];
        $transaction_last->save();
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
    private function resetInitialCapital($price,$year){
        $this->initial_capital =  $price * $this->multiple * 0.25;
        $this->variety_year[$year] = $this->initial_capital;
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

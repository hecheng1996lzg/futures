<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Trading_term extends Model
{
    protected $table = 'trading_terms' ;

    public function variety(){
        return $this->belongsTo('App\Variety');
    }

    public function year(){
        return $this->belongsToMany('App\Year', 'year_totals', 'term_id', 'year_id')->withPivot('value');
    }

    public function trading_records(){
        return $this->hasMany('App\Trading_record');
    }

    public function trading_last()
    {
        return $this->belongsTo('App\Trading_last','last_id');
    }

    public $now_num = 0; //储存计算时每年盈亏的变量
    public $now_year ; //储存每年对应模型

    public $continuities = []; //连续几日的“均线浮动值”

    public $test_num = 0; //调试用
    public $test_array = []; //调试用

    public $prev_deal = null; //上一条交易记录

    function __construct(){
        $this->now_year = new Year();
        $this->now_year->year = null;
        $this->now_year->initial_capital = null;
    }

    /**
     * 更新
     **/
    public function updateProfit_percentage($data=null,$multiple=null,$comparative_type=1){
        $this->prev_deal = $this->trading_last()->first()->trading_record()->first();
        $this->data = $data;
        $this->multiple = $multiple;
        $this->comparative_type = $comparative_type;

        /**
         * 将prev_deal之后的记录删除
         **/
        $lastRecord = Trading_record::where('term_id',$this->id)->where('datetime','>',$this->prev_deal->datetime)->get()[0];
        /* 删除前先将更新Num */
        $this->resetNum($this->prev_deal,$lastRecord);


        /**
         * 载入当前数据
         **/
        $this->now_year = $this->prev_deal->year()->first();
        $bool = $this->prev_deal->type? true:false;
        for($i=0;$i<$this->continuity;$i++){
            $this->continuities[$i] = $bool;
        }

        foreach ($this->data as $key=>$value){
            /* 最后一次交易前不进行记录 */
            if($this->prev_deal->datetime >= strtotime($value->date))continue;

            $variety = new Variety();
            $year = $variety->getYear($value->date);
            /**
             * 计算当日是否满足条件。
             **/
            if($this->continuity + $this->average - 2 <= $key){
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
            if($year != $this->now_year->year){
                $this->countYearTotal();  //总结去年的盈利
                $this->now_year = $this->resetInitialCapital($value->closing,$year);  //计算今年的初始资金
                $this->now_num = 0;
            }
            /**
             * 满足交易条件首先操作结果不能为--
             **/
            if($operation!=='--'){
                /*
                 * 第一次开始交易 或 与之前交易操作相反
                 * */
                if($this->prev_deal===null || ($operation !== ($this->prev_deal->type? true:false))){
                    $this->calculationTotal($value,$bool);
                }
            }

            /**
             * 最后一个值进行结束计算
             * 执行：保存最后一次正常交易记录
             * 结束计算：以当前价格卖掉或买入手上的商品
             **/
            if($key+1 == count($this->data)){
                /* 强制交易前存入最后一条正常交易记录 */
                $trading_last = new Trading_last();
                $trading_last->record_id = $this->prev_deal->id;
                $trading_last->save();
                $trading_term_2 = Trading_term::find($this->id);
                $trading_term_2->last_id = $trading_last->id;
                $trading_term_2->save();

                /* 判断是否最后一条刚好结束，导致重复存入 */
                if(!Year_total::where(['term_id'=>$this->id,'year_id'=>$this->now_year->id])->first()){
                    $this->calculationTotal($value, !$this->prev_deal->type);
                    $this->countYearTotal();
                }
            }
        }
        $this->clearDefaultAttr();
    }

    /**
     * 遍历日期范围内的数据，保存至数据库
     **/
    public function countProfit_percentage($data=null,$multiple=null,$comparative_type=1){
        $this->data = $data;
        $this->multiple = $multiple;
        $this->comparative_type = $comparative_type;
        foreach ($this->data as $key=>$value){
            $variety = new Variety();
            $year = $variety->getYear($value->date);
            /**
             * 计算当日是否满足条件。
             **/
            if($this->continuity + $this->average - 2 <= $key){
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
            if($year != $this->now_year->year){
                $this->countYearTotal();  //总结去年的盈利
                $this->now_year = $this->resetInitialCapital($value->closing,$year);  //计算今年的初始资金
                $this->now_num = 0;
            }

            /**
             * 满足交易条件首先操作结果不能为--
             **/
            if($operation!=='--'){
                /*
                 * 第一次开始交易 或 与之前交易操作相反
                 * */
                if($this->prev_deal===null || $operation !== $this->prev_deal->type){
                    $this->calculationTotal($value,$bool);
                }
            }

            /**
             * 最后一个值进行结束计算
             * 执行：保存最后一次正常交易记录
             * 结束计算：以当前价格卖掉或买入手上的商品
             **/
            if($key+1 == count($this->data)){
                /* 强制交易前存入最后一条正常交易记录 */
                $trading_last = new Trading_last();
                $trading_last->record_id = $this->prev_deal->id;
                $trading_last->save();
                $trading_term_2 = Trading_term::find($this->id);
                $trading_term_2->last_id = $trading_last->id;
                $trading_term_2->save();

                /* 判断是否最后一条刚好结束，导致重复存入 */
                if(!Year_total::where(['term_id'=>$this->id,'year_id'=>$this->now_year->id])->first()){
                    $this->calculationTotal($value, !$this->prev_deal->type);
                    $this->countYearTotal();
                }
            }
        }
        $this->clearDefaultAttr();
    }

    /**
     * 获取当天的均线价格
     */
    private function getAverageAboveOne($key){
        $total = 0;
        $num = 0;
        for ($i=0; $i<$this->average; $i++){
            if(isset($this->data[$key-$i])){
                $total+=$this->data[$key-$i]->closing;
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
        return $ave < $this->data[$key]->closing;
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
        return $this->data[$key-1]->closing < $this->data[$key]->closing;
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
        if($this->now_num==0)return ;
        $num = $this->now_num * $this->multiple / $this->now_year->initial_capital;
        $year_total = Year_total::where(['term_id'=>$this->id,'year_id'=>$this->now_year->id])->first();
        $year_total = $year_total? $year_total:new Year_total();
        $year_total->term_id = $this->id;
        $year_total->year_id = $this->now_year->id;
        $year_total->value = $num;
        $year_total->save();
    }

    /**
     * 计算年份初始资金&创建年份
     * 第一天的收盘价 × 手数 × 25%
     **/
    private function resetInitialCapital($price,$year_num){
        $year = Year::where(['variety_id'=>$this->variety_id,'year'=>$year_num])->first();
        if(!$year){
            $year = new Year();
            $year->variety_id = $this->variety_id;
            $year->year = $year_num;
            $year->initial_capital = $price * $this->multiple * 0.25;
            $year->save();
        }
        return $year;
    }

    /**
     * 清除自身以外属性
     **/
    public function clearDefaultAttr(){
        $this->data = null;
        $this->multiple = null;
        $this->comparative_type = null;
    }

    /**
     * 参数：$value ：一条数据
     *      $bool  ：交易类型
     * 返回值：空
     * 执行：根据交易类型，计算盈亏数值。保存这次交易时的状态和价格。
     **/
    private function calculationTotal($value, $bool){
        $trading_record = new Trading_record();
        $trading_record->term_id = $this->id;
        $trading_record->year_id = $this->now_year->id;
        $trading_record->datetime = strtotime($value->date);
        $trading_record->value = $value->closing;
        $trading_record->type = $bool;
        $trading_record->save();
        if($this->prev_deal!==null) {
            if($bool){ //买入
                $this->now_num += $this->prev_deal->value - $value->closing;
            }else{ //卖出
                $this->now_num += $value->closing - $this->prev_deal->value;
            }
        }
        $this->prev_deal = $trading_record;
    }

    /**
     * 参数：$prev ：最后一次正常交易
     *      $last  ：强制交易
     * 返回值：空
     * 执行：复原$num
     **/
    private function resetNum($prev,$last){
        $num = Year_total::where(['year_id'=>$prev->year_id,'term_id'=>$prev->term_id])->first();
        $num_value = $num->year()->get()[0]->initial_capital*$num->value/$this->variety()->get()[0]->multiple;
        if($prev->id==$last->id || $last->year_id != $num->year_id){
            return ;
        }else{
            if($last->type){ //买入
                $this->now_num = $num_value - ($prev->value - $last->value);
            }else{ //卖出
                $this->now_num = $num_value - ($last->value - $prev->value);
            }
        }
    }

}

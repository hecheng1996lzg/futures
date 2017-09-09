<?php

namespace App\Http\Controllers;

use App\Deal;
use Illuminate\Http\Request;

class CountController extends Controller
{
    public $data = []; //上传文本的二维数组格式

    public $results = []; //最后表格结果集
    public $results_weight = []; //最后结果的加权值
    public $results_year_detail = []; //按连续几日分类，年份和均线详细信息

    public $min_year = 2012; //默认初始年份
    public $max_year = 2017; //默认最大年份
    public $min_date = '2012-01-01'; //默认初始年份时间戳
    public $max_date = '2018-01-01'; //默认最大年份时间戳

    public $multiple = 10; //默认手数

    public $max_continuity = 10; //默认连续10
    public $max_average = 51;  //默认均线51

    public function index(){
        return view('index');
    }

    public function calculation(Request $request){
        $validator = \Validator::make($request->input(),[
            '*' => 'required',
        ]);
        if($validator->fails() || !$request->hasFile('fileText')){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        /**
         * 初始化数据
         * 1、将文件读入数组
         * 2、记录参数
         */
        $this->multiple = $request->input('multiple');
        $this->max_continuity = $request->input('max_continuity');
        $this->max_average = $request->input('max_average');

        $path = $_FILES['fileText']['tmp_name'];

        $deal = new Deal();
        $this->min_year = $deal->getYear($request->input('min_year'));
        $this->max_year = $deal->getYear($request->input('max_year'));
        unset($deal);

        $this->min_date = strtotime($request->input('min_year'));
        $this->max_date = strtotime($request->input('max_year'));
        $this->initialData($path);

        /**
         * 参数不同对比情况，决定是否进行买卖操作
         * 1、将当日与前几日均线对比，且连续几天
         * 2、将当日均线与前几日均线对比，且连续几天
         * 3、将当日与昨天对比，且连续几天
         **/
        $comparative_type = $request->input('comparative_type'); //对比方式
        switch ($comparative_type){
            case 1:
                break;
            case 2:
                break;
            case 3:
                $this->max_average = 2;
                break;
        }
        for($i=1; $i<=$this->max_continuity; $i++){
            for($j=2; $j<=$this->max_average; $j++){
                $deal = new Deal($this->data, $i, $j, $this->multiple, $comparative_type);
                $this->results[$i][$j] = $deal->countProfit_margin();
                unset($deal);
            }
        }

        return view('table',[
            'results'=>$this->results,
            'continuity'=>$this->max_continuity,
            'average'=>$this->max_average,
        ]);
    }

    public function calculation2(Request $request){
        $validator = \Validator::make($request->input(),[
            '*' => 'required',
        ]);
        if($validator->fails() || !$request->hasFile('fileText')){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        /**
         * 初始化数据
         * 1、将文件读入数组
         * 2、记录参数
         */
        $this->multiple = $request->input('multiple');
        $this->max_continuity = $request->input('max_continuity');
        $this->max_average = $request->input('max_average');

        $path = $_FILES['fileText']['tmp_name'];

        $deal = new Deal();
        $this->min_year = $deal->getYear($request->input('min_year'));
        $this->max_year = $deal->getYear($request->input('max_year'));
        unset($deal);

        $this->min_date = strtotime($request->input('min_year'));
        $this->max_date = strtotime($request->input('max_year'));
        $this->initialData($path);

        /**
         * 参数不同对比情况，决定是否进行买卖操作
         * 1、将当日与前几日均线对比，且连续几天
         * 2、将当日均线与前几日均线对比，且连续几天
         * 3、将当日与昨天对比，且连续几天
         **/
        $comparative_type = $request->input('comparative_type'); //对比方式
        switch ($comparative_type){
            case 1:
                break;
            case 2:
                break;
            case 3:
                $this->max_average = 2;
                break;
        }
        for($i=1; $i<=$this->max_continuity; $i++){
            for($j=2; $j<=$this->max_average; $j++){
                $deal = new Deal($this->data, $i, $j, $this->multiple, $comparative_type);
                $this->results[$i][$j] = $deal->countProfit_percentage();
                $this->results_weight[$i][$j] = $deal->prevailingThanAverage_Weight();
                foreach ($deal->total as $key=>$value){
                    $this->results_year_detail[$i][$key][$j] = $value;
                }
                unset($deal);
            }
        }

        $list = $this->createList($this->results,'百分比');
        $weightList = $this->createList($this->results_weight,'加权值');
        $everyYear = $this->createListYear();
        $request->session()->put('download', $list."\r\n".$weightList."\r\n".$everyYear);

        return view('table',[
            'results'=>$this->results,
            'results_weight'=>$this->results_weight,
            'continuity'=>$this->max_continuity,
            'average'=>$this->max_average,
        ]);

    }

    public function download(Request $request){
        header( "Content-type: application/vnd.ms-excel; charset=gb2312" );
        header("Content-disposition:attachment;filename=text.csv");
        echo mb_convert_encoding($request->session()->get('download'),'gb2312');
    }


    /**
     * 在设置了最小年份，最大年份后。
     * 该函数会将传入文件，裁剪后只保留满足年份条件的范围
     * 储存在 $this->data 中
     **/
    private function initialData($path){
        $data = file_get_contents($path);
        $data = explode("\r\n",$data);

        array_shift($data);
        array_pop($data);

        foreach ($data as $key=>$value){
            $row = explode(" ",$value);
            $row_time = strtotime($row[0]);
            if($row_time >= $this->min_date && $row_time <= $this->max_date){
                $this->data[] = $row;
            }
        }
    }

    private function createList($data,$title){
        $str = $title."\r\n";
        for($i=0; $i<=$this->max_continuity; $i++) {
            for ($j = 1; $j <= $this->max_average; $j++) {
                if($i==0){
                    if($j==1){
                        $str.=' ';
                    }else{
                        $str.=($j);
                    }
                }
                else if($i!=0&&$j==1){
                    $str.=($i);
                }else{
                    $str.=$data[$i][$j];
                }

                if($j<$this->max_average){
                    $str.=',';
                }
            }
            $str .= "\r\n";
        }
        return $str;
    }

    private function createListYear(){
        $str = "";
        foreach ($this->results_year_detail as $ck=>$cv){
            $str.= $ck."日连续";
            for ($i=2; $i<=$this->max_average; $i++){
                $str.= ','.$i;
            }
            $str.="\r\n";
            for ($i=$this->min_year; $i<=$this->max_year; $i++){
                if(!isset($this->results_year_detail[$ck][$i]))continue;
                $str.= $i;
                for ($j=2; $j<=$this->max_average; $j++){
                    $val = isset($this->results_year_detail[$ck][$i][$j])? round($this->results_year_detail[$ck][$i][$j]*100,2).'%':'';
                    $str.= ','.$val;
                }
                $str.= "\r\n";
            }
            $str.= "\r\n";
        }
        return $str;
    }
}



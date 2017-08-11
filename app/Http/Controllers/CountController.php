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

    public $min_year = 2012; //初始年份
    public $max_year = 2017; //最大年份

    public $multiple = 10; //默认手数

    public $max_continuity = 10; //默认连续10
    public $max_average = 51;  //默认均线51

    public function index(){
        return view('index');
    }

    public function calculation(Request $request){
        $path = $_FILES['fileText']['tmp_name'];

        $this->min_year = $request->input('min_year');
        $this->max_year = $request->input('max_year');
        $this->initialData($path);

        for($i=1; $i<=$this->max_continuity; $i++){
            for($j=2; $j<=$this->max_average; $j++){
                $deal = new Deal($this->data, $i, $j, $this->multiple);
                $this->results[$i][$j] = $deal->calculationDetail();
                $this->results_weight[$i][$j] = $deal->calculationWeight();
                foreach ($deal->total as $key=>$value){
                    $this->results_year_detail[$i][$key][$j] = $value;
                }
            }
        }

        $list = $this->createList($this->results,'百分比');
        $weightList = $this->createList($this->results_weight,'加权值');
        header( "Content-type: application/vnd.ms-excel; charset=gb2312" );
        header("Content-disposition:attachment;filename=text.csv");
        echo mb_convert_encoding($list."\r\n".$weightList."\r\n".$this->createListYear(),'gb2312');
    }

    private function initialData($path){
        $data = file_get_contents($path);
        $data = explode("\r\n",$data);

        array_shift($data);
        array_pop($data);

        foreach ($data as $key=>$value){
            $row = explode(" ",$value);
            $deal = new Deal();
            if($deal->getYear($row[0]) >= $this->min_year && $deal->getYear($row[0]) <= $this->max_year){
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

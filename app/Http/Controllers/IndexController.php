<?php

namespace App\Http\Controllers;

use App\Variety;
use App\Year_total;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index(){
        return view('index');
    }

    public function download(Request $request,$id){
        $variety = Variety::find($id);
        $trading_terms = $variety->trading_terms()->orderBy('continuity')->orderBy('average')->get();

        /* 初始化list */
        $list = [];
        foreach ($trading_terms as $value){
            $num = 0;
            foreach($value->year()->get() as $v){
                $num += $v->pivot->value;
            }
            $list[$value->continuity][$value->average] = round($num*100,2).'%';
        }

        /* 初始化weightList */
        $weightList = [];
        foreach ($trading_terms as $value){
            $num = 1;
            foreach($value->year()->get() as $v){
                $num *= $v->pivot->value+1;
            }
            $weightList[$value->continuity][$value->average] = round($num,2);
        }

        $list = $this->createList($list,'百分比');
        $weightList = $this->createList($weightList,'加权值');
        $yearList = $this->createListYear($variety);
        header( "Content-type: application/vnd.ms-excel; charset=gb2312" );
        header("Content-disposition:attachment;filename=text.csv");
        echo mb_convert_encoding($list."\r\n".$weightList."\r\n".$yearList,'gb2312');

    }

    private function createList($data,$title){
        $str = $title."\r\n";
        for($i=0; $i<=count($data); $i++) {
            for ($j = 1; $j <= count($data[1])+1; $j++) {
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

                if($j<count($data[1])+1){
                    $str.=',';
                }
            }
            $str .= "\r\n";
        }
        return $str;
    }

    private function createListYear($variety){
        $str = "";

        for($i=1;$i<=$variety->continuity;$i++){
            $str.= $i."日连续";
            for ($j=2; $j<=$variety->average; $j++){
                $str.= ','.$j;
            }
            $str.="\r\n";
            foreach ($variety->years()->orderBy('year')->get() as $value){
                $str.= $value->year;
                for ($j=2; $j<=$variety->average; $j++){
                    $terms = $variety->trading_terms()->where('continuity',$i)->where('average',$j)->first();
                    $total = Year_total::where('term_id',$terms->id)->where('year_id',$value->id)->first();
                    $str.= ','.round($total->value*100,2).'%';
                }
                $str.= "\r\n";
            }
            $str.= "\r\n";
        }
        return $str;
    }


}

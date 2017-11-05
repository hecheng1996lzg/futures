<?php

namespace App\Http\Controllers;

use App\Trading_term;
use App\Transaction_result;
use App\Variety;
use App\Variety_data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VarietyController extends Controller
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


    public function create(Request $request){
        return view('variety.add');
    }

    public function store(Request $request){
        /* 参数验证 */
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
        $this->min_date = strtotime($request->input('min_year'));
        $this->max_date = strtotime($request->input('max_year'));

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

        $path = $_FILES['fileText']['tmp_name'];
        $fileName = $_FILES['fileText']['name'];

        /* 判断产品是否存在，存在则报错 */
        $variety = Variety::where('name',$fileName)->first();
        if($variety){
            dd('产品存在');
        }

        /* 新建产品 */
        DB::beginTransaction();
        $variety = new Variety();
        $variety->name = $fileName;
        $variety->min_date = $this->min_date;
        $variety->max_date = $this->max_date;
        $variety->continuity = $this->max_continuity;
        $variety->average = $this->max_average;
        $variety->comparative_type = $comparative_type;
        $variety->multiple = $this->multiple;
        $variety->save();

        /* 数据截取 */
        $data = $variety->cutData($path,$variety->min_date,$variety->max_date);
        /* 存入产品信息 */
        $variety->saveFragment($data);

        /* 取出所有数据 */
        $data = Variety_data::orderBy('date','asc')->get();

        /* 交易记录更新 */
        for($i=1; $i<=$this->max_continuity; $i++){
            for($j=2; $j<=$this->max_average; $j++){
                $trading_terms = new Trading_term();
                $trading_terms->variety_id = $variety->id;
                $trading_terms->continuity = $i;
                $trading_terms->average = $j;
                $trading_terms->save();
                $this->results[$i][$j] = $trading_terms->countProfit_percentage($data, $variety->multiple, $variety->comparative_type);

                /*$this->results_weight[$i][$j] = $deal->prevailingThanAverage_Weight();
                foreach ($deal->total as $key=>$value){
                    $this->results_year_detail[$i][$key][$j] = $value;
                }
                unset($deal);*/
            }
        }
        DB::commit();

        dd('ok');
            /* 产品数据取出 */

        /* 年份总更新 */




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

        $variety->name = rtrim($_FILES['fileText']['name'],'.txt');
        $variety->min_date = $this->min_date;
        $variety->max_date = $this->max_date;
        $variety->continuity = $this->max_continuity;
        $variety->average = $this->max_average;
        $variety->comparative_type = $comparative_type;
        $variety->multiple = $this->multiple;
        $variety->save();

        for($i=1; $i<=$this->max_continuity; $i++){
            for($j=2; $j<=$this->max_average; $j++){
                $transaction_result = new Transaction_result();
                $this->results[$i][$j] = $transaction_result->saveRecord($this->data, $i, $j, $this->multiple, $comparative_type, $variety->id);
                foreach ($transaction_result->total as $key=>$value){
                    $this->results_year_detail[$i][$key][$j] = $value;
                }
                unset($transaction_result);
            }
        }
        DB::commit();

        /**
         * 上传文件,使用iconv 防止中文乱码
         **/
        $name = $_FILES["fileText"]["name"];
        $name=iconv("UTF-8","gb2312", $name);
        $test = move_uploaded_file($path,'../txt_resource/'.$name);
        $name=iconv("gb2312","UTF-8", $name);

        return redirect('variety/'.$variety->id);
    }

    public function show($id){
        $variety = Variety::find($id);
        return view('variety.index',['variety'=>$variety]);
    }

    public function edit(){
        return view('variety.edit');
    }

    public function update(Request $request){
        /* 参数验证 */
        $validator = \Validator::make($request->input(),[
            '*' => 'required',
        ]);
        if($validator->fails() || !$request->hasFile('fileText')){
            return redirect()->back()->withErrors($validator)->withInput();
        }

        /**
         * 读取文件
         **/
        $path = $_FILES['fileText']['tmp_name'];
        $fileName = $_FILES['fileText']['name'];

        /**
         * 查找产品是否存在
         **/
        $variety = Variety::where('name',$fileName)->first();
        if(!$variety)dd('该产品不存在，请先上传');

        /**
         * 更新初始数据
         * 1、将文件读入数组
         * 2、记录新数据
         */
        $this->multiple = $variety->multiple;
        $this->max_continuity = $variety->continuity;
        $this->max_average = $variety->average;

        $data_last = $variety->variety_data()->orderBy('date','desc')->first();
        $this->min_date = strtotime($data_last->date)+60*60*24;
        $data = $variety->cutData($path, $this->min_date);
        if(!$data)dd('数据没更新');

        /* 存入产品信息 */
        DB::beginTransaction();
        $variety->saveFragment($data);

        /* 取出所有数据 */
        $data = Variety_data::orderBy('date','asc')->get();

        /* 交易记录更新 */
        for($i=1; $i<=$this->max_continuity; $i++){
            for($j=2; $j<=$this->max_average; $j++){
                $trading_terms = Trading_term::where(['variety_id'=>$variety->id,'continuity'=>$i,'average'=>$j])->first();
                $this->results[$i][$j] = $trading_terms->updateProfit_percentage($data, $variety->multiple, $variety->comparative_type, $this->min_date);
            }
        }

        DB::commit();

        dd('ok');
    }

}

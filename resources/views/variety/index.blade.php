@extends('layouts.layout1')
@section('content')
    <article class="list-content">
        <h2>数据列表 <span></span></h2>
        <section class="list-content-condition">
            <ul class="list-control info">
                <li>
                    <input id="range" type="text" class="input-text">
                    <a class="btn btn-primary btn-lg ripple">保存选择</a>
                    <a class="btn btn-primary btn-lg ripple reset">重置</a>
                </li>
                <li>共：<span>0</span></li>
                <li>买：<span>0</span></li>
                <li>卖：<span>0</span></li>
                <li>荐：<span>无</span></li>
            </ul>
            <ul class="list-control">
                <li></li>
                <li>
                    <a class="iconfont icon-add">新增</a>
                </li>
                <li>
                    <a class="iconfont icon-biaoxing">保存</a>
                </li>
                <li>
                    <a href="{{ asset('download') }}" target="_blank" class="iconfont icon-xiangshang5">导出</a>
                </li>
            </ul>
        </section>
        <section style="overflow-x: scroll;">
            <table class="list-table">
                <thead>
                <tr>
                    <th></th>
                    @for($i = 2; $i <= $variety->average; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                @for($i = 1; $i <= $variety->continuity; $i++)
                    <tr>
                        <td>{{ $i }}</td>
                        @for($j = 2; $j <= $variety->average; $j++)
                            <?php $v = App\Transaction_result::where(['variety_id'=>$variety->id,'continuity'=>$i,'average'=>$j])->first()->transaction_years->sum('value') ?>
                            <td style="color: {{ $v<0? 'rgb(0,255,0)':'rgb(255,255,255)' }};">{{ $v }}%</td>
                        @endfor
                    </tr>
                @endfor
                </tbody>
            </table>
        </section>
        <section class="list-content-page">
                <span>
                    第<input class="border-bottom-input" type="text" value="1" />共<em>1</em>页
                </span>
                <span>
                    显示<em>1</em>到<em>10</em>，共<em>10</em>记录
                </span>
        </section>
    </article>
@stop

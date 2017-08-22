@extends('layout')
@section('content')
    <article class="list-option">
        <form id="addForm"  method="post" enctype="multipart/form-data" action="{{ asset('count/index') }}">
            <section class="list-add form-tab list-option-show">
                <h2>
                    <span>连续几日&均值计算</span>
                    <div class="container">
                        <div class="bg_con">
                            <input id="checked_1" type="checkbox" class="switch" />
                            <label for="checked_1"></label>
                        </div>
                    </div>
                </h2>
                <div class="list-add-content content">
                    <section>
                        <div>
                            <label for="min_year">开始日期：</label>
                            <p>
                                <input id="min_year" name="min_year" type="date" class="input-text" placeholder="请输入开始年份" value="2012-01-01" required>
                            </p>
                        </div>
                        <div>
                            <label for="max_year">结束日期：</label>
                            <p>
                                <input id="max_year" name="max_year" type="date" class="input-text" placeholder="请输入结束年份" value="2017-12-31" required>
                            </p>
                        </div>
                        <div>
                            <label for="max_continuity">连续天数：</label>
                            <p>
                                <input id="max_continuity" name="max_continuity" type="number" class="input-text" placeholder="请输入连续天数" value="10" required>
                            </p>
                        </div>
                        <div>
                            <label for="max_average">几日均线：</label>
                            <p>
                                <input id="max_average" name="max_average" type="number" class="input-text" placeholder="请输入几日均线" value="51" required>
                            </p>
                        </div>
                    </section>
                    <section>
                        <div class="w100">
                            <label>请选择上传文件，可以拖入：{{ $errors->first('fileText') }}</label>
                            <p>
                                <input name="fileText" type="file" class="input-text" placeholder="请选择上传文件，可以拖入">
                            </p>
                        </div>
                        <div>
                            <label>比较方式：{{ $errors->first('comparative_type') }}</label>
                            <p>
                                <select name="comparative_type" class="input-text">
                                    <option value="1">默认: 均线对比均线</option>
                                    <option value="2">当日均线比前一日均线</option>
                                    <option value="3">当日价格比前一日价格</option>
                                </select>
                            </p>
                        </div>
                        <div>
                            <label for="multiple">手数：</label>
                            <p>
                                <input id="multiple" name="multiple" type="number" class="input-text" placeholder="请输入手数" value="10" required>
                            </p>
                        </div>
                    </section>
                </div>
                <div class="list-add-btn form-btn">
                    <button id="addFormSubmit" class="btn btn-primary btn-lg ripple">提交</button>
                    <button id="removeFormSubmit" class="btn btn-primary btn-lg ripple">关闭</button>
                </div>
            </section>
        </form>
    </article>
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
                    @for($i = 2; $i <= $average; $i++)
                        <th>{{ $i }}</th>
                    @endfor
                </tr>
                </thead>
                <tbody>
                @foreach($results as $key=>$value)
                    <tr>
                        <td>{{ $key }}</td>
                        @foreach($results[$key] as $v)
                            <td style="color: {{ $v<0? 'rgb(0,255,0)':'rgb(255,255,255)' }};">{{ $v }}%</td>
                        @endforeach
                    </tr>
                @endforeach
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

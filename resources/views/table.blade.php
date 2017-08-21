<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/toggle_animation.css') }}" />
    <script src="{{ asset('assets/js/jquery-1.7.1.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.form.js') }}"></script>
    <script src="{{ asset('assets/js/line.js') }}"></script>
    <script src="{{ asset('assets/js/vector.js') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>
    <script src="{{ asset('assets/js/log.js') }}"></script>
</head>
<body>

<header class="header">
    <img class="logo" src="{{ asset('assets/images/logo.PNG') }}" alt=""/>
    <menu>
        <ul class="border-light border-light-bottom">
            <li class="active">
                <p class="iconfont icon-shouye"><a href="#"> 我的工作台</a></p>
            </li>
            <li>
                <p class="iconfont icon-leimu"><a href="#"> 我的参数</a></p>
            </li>
            <li>
                <p class="iconfont icon-bangzhuzhongxin"><a href="#"> 系统配置</a></p>
            </li>
        </ul>
    </menu>
    <div class="ucenter">
        <img src="{{ asset('assets/images/user.jpg') }}" alt="">
        <span>olaolaola</span>
        <span>退出</span>
    </div>
</header>

<div class="body">
    <aside>
<!--
        <div class="user">
            <img src="images/user.jpg" alt=""/>
        </div>
-->
        <ul class="sidebar">
            <li>
                <h3>系统功能</h3>
                <ul class="border-light border-light-right">
                    <li class="active">
                        <p class="iconfont icon-duozhongzhifu">单纯价格系统</p>
                    </li>
                    <li>
                        <p class="iconfont icon-duozhongzhifu">开发中...</p>
                    </li>
                </ul>
            </li>
        </ul>
    </aside>
    <main>
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
                                <label for="min_year">开始年份：</label>
                                <p>
                                    <input id="min_year" name="min_year" type="text" class="input-text" placeholder="请输入开始年份" value="2012" required>
                                </p>
                            </div>
                            <div>
                                <label for="max_year">结束年份：</label>
                                <p>
                                    <input id="max_year" name="max_year" type="text" class="input-text" placeholder="请输入结束年份" value="2017" required>
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
                        </section>
                        <section>
                            <div class="w100">
                                <label>请选择上传文件，可以拖入：{{ $errors->first('fileText') }}</label>
                                <p>
                                    <input name="fileText" type="file" class="input-text" placeholder="请选择上传文件，可以拖入">
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
            <h2>百分比 <span></span></h2>
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
                        <td>{{ $v }}</td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <section class="list-content-page">
                <span>
                    第<input class="border-bottom-input" type="text" value="1" />共<em>1</em>页
                </span>
                <span>
                    显示<em>1</em>到<em>5</em>，共<em>5</em>记录
                </span>
            </section>
        </article>
        <footer><span>YUNPHANT BLOCKCHAIN 2017</span></footer>
    </main>
</div>

<div class="alter-layout" style=" display: none;">
    <article class="log">
        <ul>
            <li><i></i><span>表单提交</span></li>
            <li><i></i><span>交易转发</span></li>
            <li><i></i><span>vp0接收交易请求</span></li>
            <li><i></i><span>表单提交</span></li>
            <li><i></i><span>vp0打包并分发交易请求</span></li>
            <li><i></i><span>vp0接收交易请求</span></li>
            <li><i></i><span>表单提交</span></li>
            <li><i></i><span>vp0打包并分发交易请求</span></li>
            <li><i></i><span>vp0接收交易请求</span></li>
        </ul>
    </article>
    <article class="force">
        <svg viewbox="-400 -400 800 800 " width="300" height="300">
            <path id="links" stroke-width="5" stroke="gray"/>
            <path id="dir" d="" fill="transparent" ></path>
            <defs>
                <filter id="f1" x="0" y="0" width="200%" height="200%">
                    <feOffset result="SourceAlphaDeviated" in="SourceGraphic" dx="20" dy="20"/>
                    <feOffset result="ShadowDeviated" in="SourceAlpha" dx="20" dy="20"/>
                    <feGaussianBlur result="blurOut" in="ShadowDeviated" stdDeviation="8" />
                    <feBlend in="SourceAlphaDeviated" in2="blurOut" mode="normal" />
                </filter>
            </defs>
        </svg>
    </article>
</div>
</body>
</html>
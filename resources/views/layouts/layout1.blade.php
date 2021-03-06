<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/toggle_animation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/iconfont.css') }}" />
    <script src="{{ asset('assets/script/jquery-3.1.1.js') }}"></script>
    <script src="{{ asset('assets/script/select.js') }}"></script>
    <script src="{{ asset('assets/script/script.js') }}"></script>
</head>
<body>
@yield('style')

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
                <p class="iconfont icon-shezhi"><a href="#"> 系统配置</a></p>
            </li>
        </ul>
    </menu>
    <div class="ucenter">
        <span>olaolaola</span>
        <span>退出</span>
    </div>
</header>

<div class="body">
    <aside>
        <div class="user">
            <img src="{{ asset('assets/images/user.jpg') }}" alt=""/>
        </div>
        <ul class="sidebar">
            <li>
                <h3>欢迎使用futures V2.18</h3>
                <ul class="border-light border-light-right">
                    <li class="row">
                        <p><a class="iconfont icon-duozhongzhifu" href="{{ asset('/') }}">首页</a></p>
                    </li>
                </ul>
            <li>
                <h3>系统功能</h3>
                <ul class="border-light border-light-right">
                    <li class="rows">
                        <p class="iconfont icon-zhengli">
                            <span>产品列表</span>
                            <i class="iconfont icon-xiangxia2"></i>
                        </p>
                        <ul style="display: none">
                            <li class="row active">
                                <p><a href="{{ asset('variety/add') }}">增加产品+</a></p>
                            </li>
                            @foreach($varieties as $value)
                            <li class="row"><p><a href="{{ asset('variety/'.$value->id) }}">{{ $value->name }}</a></p></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="row">
                        <p><a class="iconfont icon-duozhongzhifu" href="{{ asset('variety/update/all') }}">一键更新</a></p>
                    </li>
                    <li class="row">
                        <p><a class="iconfont icon-duozhongzhifu" href="#">系统二列表</a></p>
                    </li>
                </ul>
            </li>
        </ul>
    </aside>
    <main>
        @yield('content')
        <footer><span>FUTURES SYSTEM 2017</span></footer>
    </main>
</div>

@yield('script')

</body>
</html>
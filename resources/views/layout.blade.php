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
        <img src="{{ asset('assets/images/user.jpg') }}" alt="">
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
        @yield('content')
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

@yield('script')

</body>
</html>
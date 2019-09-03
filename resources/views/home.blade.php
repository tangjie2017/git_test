<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>BIS</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link href="{{ asset('css/layout.css?'.config('app.version')) }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css?'.config('app.version')) }}" media="all">
    <link href="{{ asset('css/tabstyle.css?'.config('app.version')) }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('layui/layui.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-1.11.3.min.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/korbin.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/tab.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/tool.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/getdate.js?'.config('app.version')) }}"></script>

    <style>body{overflow-x: hidden;}</style>
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
        window.timeZone = '{{ \App\Auth\Common\CurrentUser::getCurrentUser()->wareTime }}';
        //语言参数
        var locale_message_language = "{{ \Illuminate\Support\Facades\Session::get('lang') ? \Illuminate\Support\Facades\Session::get('lang') : 'zh_CN' }}";
    </script>
    @include('layouts.language-js')
</head>
<body>
<div class="kbin_head fclear">
    <div class="logo"><a class="kba" href="{{ url('/') }}">BIS</a></div>
    <div class="navbar-right">
        <ul class="layui-nav">
            <li class="layui-nav-item">
                <a href="javascript:;" class="timeZone" data="{{ $ware_house_time_zone }}">{{ __('auth.CurrentLoginTimeZone') }} [{{ $ware_house }}( {{ $ware_house_time_zone }} )]：</a>
                <dl class="layui-nav-child">
                    <div class="nowdate"><a href="#" data="{{ $old_time_zone }}" class="timeZone"><p style="color: black"></p></a></div>
                    <input type="hidden" data="{{ $old_time_zone }}" value="{{ $old_ware_house }}" id="oldTimeZone"/>
                </dl>
            </li>
            <li class="layui-nav-item">
                <a href="#"><img src="img/user1.png" class="layui-nav-img">{{ $user_code }}</a>
                <dl class="layui-nav-child">
                    <dd><a href="{{ url('logout') }}">{{ __('auth.logout') }}</a></dd>
                </dl>
            </li>
        </ul>
    </div>
</div>
<div class="leftnav">
    <div class="submenu-left">
        @foreach ($list as $key => $value)
            <div class="col">

            @if(isset($value['url']))
                <a class="kba" href="{{ url($value['url']) }}"><h2 class="kbico"><i class="layui-icon layui-icon-home"></i>{{ $value['title'] }}</h2></a>
            @else
                <a  href="javascript:void(0);"><h2 class="kbico"><i class="layui-icon layui-icon-home"></i>{{ $value['title'] }}</h2></a>
            @endif
            @if(isset($value['children']))
                <ul  class="xiala xl1">
                    @foreach($value['children'] as $twoMenu)
                        @if(isset($twoMenu['url']))
                            <li><a class="kba" href="{{ url($twoMenu['url']) }}">{{ $twoMenu['title'] }}</a></li>
                        @else
                            <li><a  href="javascript:void(0);">{{ $twoMenu['title'] }}</a></li>
                        @endif
                    @endforeach
                </ul>
            @endif

            </div>
        @endforeach

    </div>
    <div class="righttext">
        <!--iframe Start-->
        <div id="page-tab">
            <button class="tab-btn" id="page-prev"></button>
            <nav id="page-tab-content">
                <div id="menu-list">
                    <a href="javascript:void(0);" data-url="{{ url('index') }}" data-value="{{ __('auth.home') }}"  class="defaultTab homebtn active">{{ __('auth.home') }}</a>
                </div>
            </nav>
            <button class="tab-btn" id="page-next"></button>
            <div id="page-operation">
                <div id="menu-all">
                    <ul id="menu-all-ul">
                        <li class="closeOther">{{ __('auth.CloseOtherTabs') }}</li>
                        <li class="closeAll">{{ __('auth.CloseAllTabs') }}</li>
                    </ul>
                </div>
            </div>
        </div>

        <div id="page-content" style="">
            <iframe class="iframe-content defaultIframe active" data-url="{{ url('index') }}" data-value="{{ __('auth.home') }}" src="{{ url('index') }}"></iframe>
        </div>
    </div>
</div>




<script>
    $(".submenu-left .kba").tab();

    layui.use('element', function(){
        var element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块
        var $ = layui.jquery;
        //监听导航点击
        element.on('nav(demo)', function(elem){
            //console.log(elem)
            layer.msg(elem.text());
        });
    });

    $('.timeZone').click(function () {
        if($(this).attr('data') == window.timeZone){
            return;
        }

        $.MXAjax({
            type:'post',
            data: {
                'timeZone':parseInt($(this).attr('data')),
                '_token':"<?php echo (csrf_token()); ?>"
            },
            dataType: 'json',
            url:'timeZone',
            success:function (response) {
                window.temp = '';
                window.location.href = '/';
            }
        });
    });
</script>
</body>
</html>
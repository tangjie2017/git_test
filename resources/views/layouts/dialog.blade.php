<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>BIS</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css?'.config('app.version')) }}" media="all">
    <link href="{{ asset('css/tabstyle.css?'.config('app.version')) }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('layui/layui.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-1.11.3.min.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/korbin.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/tab.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/highcharts-6.2.0.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/highcharts-cn.js?'.config('app.version')) }}"></script>
    <script type="text/javascript" src="{{ asset('js/kbPulic.js?'.config('app.version')) }}"></script>

    @yield('css')
    <script type="text/javascript">
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
        window.timeZone = '{{ \App\Auth\Common\CurrentUser::getCurrentUser()->wareTime }}';
        //语言参数
        var locale_message_language = "{{ \Illuminate\Support\Facades\Session::get('lang') ? \Illuminate\Support\Facades\Session::get('lang') :  'zh_CN' }}";
    </script>
    @include('layouts.language-js')
</head>
<body>


@yield('content')

@yield('javascripts')
<script type="text/javascript" src="{{ asset('js/tool.js?'.config('app.version')) }}"></script>
</body>
</html>
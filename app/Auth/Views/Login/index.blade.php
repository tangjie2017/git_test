<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>{{ __('auth.BISLogin') }}</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}" media="all">
    <script type="text/javascript">
        //语言
        var locale_message_language = "{{ \Illuminate\Support\Facades\Session::get('lang') ? \Illuminate\Support\Facades\Session::get('lang') : 'zh_CN' }}";
    </script>
</head>
<body class="graybg">
<div class="JYloginbg">
    <div class="JYlogPanel">
        <div class="logintitle">
            <h2>{{ __('auth.BISLogin') }}</h2>
        </div>
        <form class="kb_form" action="index" id="myForm">
            {{ csrf_field() }}
            <input type="hidden" name="wareCode" id="wareCode" value="" />
            <ul class="kb_ul">
                <li>
                    <input type="text" name="userCode" placeholder="{{ __('auth.userCode') }}" value="{{ $userCode }}" />
                </li>
                <li>
                    <input type="password" name="password" placeholder="{{ __('auth.password') }}" value="123456"/>
                    <div class="remind"></div>
                </li>
                <li>
                    <button type="button" id="doLogin">{{ __('auth.login') }}</button>
                </li>
            </ul>
        </form>
    </div>

    {{--仓库弹窗--}}
    <div  id="warehouse" style="display:none;width:260px; height:80px; margin:20px;padding: 10px 20px">
            <div class="inputBlock">
                <div class="multLable">
                    <em data-id= 'USEA'>{{ __('auth.USEastWarehouse') }}</em>
                    <em data-id= 'USWE'>{{ __('auth.USWestWarehouse') }}</em>
                </div>
            </div>
    </div>
    <select name="language" class="language" onchange="changeLan()" style="margin-left: 48%;border:0px solid #ccc;background: #f2f2f2;">
        @foreach($language as $code => $lang)
            <option value="{{ $code }}" {{ session()->get('lang') == $code ? 'selected' : '' }}>{{ $lang }}</option>
        @endforeach
    </select>
</div>
<script src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>
<script src="{{ asset('js/tool.js') }}"></script>
<script type="text/javascript" src="{{ asset('layui/layui.js') }}"></script>
@include('layouts.language-js')
<script src="{{ asset('js/login.js?'.config('app.version')) }}"></script>
<script>
    layui.use(['form','table'], function() {
        var layer = layui.layer,
            form = layui.form,
            table = layui.table;
        var element = layui.element;
        var $ = layui.jquery;

        $(function () {
            layer.open({
                type: 1,
                closeBtn: 0,
                title: $.getMessage('SelectWarehouse'),
                area: ['300px', '200px'],
                content: $('#warehouse'),
            });
        })
        //去掉提示
        $('.remind').html('');
    })
</script>
</body>
</html>
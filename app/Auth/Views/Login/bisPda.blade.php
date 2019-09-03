<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>pda登陆页</title>
    <link rel="stylesheet" href="{{ asset('css/pda.css') }}">
    <style>
        .multLable em {
            line-height: 40px;
            padding:0 15px;
            border-radius: 3px;
            background-color: #fff;
            border: 1px solid #ccc;
            margin: 10px;
            display: inline-block;
            vertical-align: top;
            height: 40px;
            margin-right: 5px;
            cursor: pointer;
        }
    </style>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css') }}" media="all">
    <script type="text/javascript" src="{{ asset('layui/layui.js') }}"></script>
    <script type="text/javascript">
        //语言
        var locale_message_language = "{{ \Illuminate\Support\Facades\Session::get('lang') ? \Illuminate\Support\Facades\Session::get('lang') : 'zh_CN' }}";
    </script>
</head>
<body>
<div class="pdabg">
    <img src="{{ asset('img/bg.jpg') }}" alt="">
    <h5>BIS</h5>
    <div class="pdalogin">
        <form action="#" class="layui-form" id="myForm">
            {{ csrf_field() }}
            <input type="hidden" name="wareCode" id="wareCode" value="" />
            <input type="hidden" name="redirect" value="{{ url('pda/index') }}">
            <div class="box">
                <img src="{{ asset('img/yh.png') }}" alt="">
                <input type="text"   name="userCode" maxlength="30" placeholder="{{ __('auth.userCode') }}">
            </div>
            <div class="box"><img src="{{ asset('img/mm.png') }}" alt=""><input type="password"  name="password" maxlength="30" placeholder="{{ __('auth.password') }}"></div>

            {{--<div class="yz"><input type="text" class="logicon2" placeholder="验证码"><img src="{{ asset('img/yz.png') }}" alt=""></div>--}}
            <p class="error remind"></p>
            <button type="button" class="logbut1" id="doLogin">{{ __('auth.login') }}</button>
        </form>

    </div>

    <!-- 弹窗内容 -->
    <div class="hide stylebut" id="warehouse" style="display: none" >
            <div class="multLable">
                <em data-id= 'USEA'>{{ __('auth.USEastWarehouse') }}</em>
                <em data-id= 'USWE'>{{ __('auth.USWestWarehouse') }}</em>
            </div>
    </div>

    <div class="choss">
        <select name="language" class="language" onchange="changeLan()" style="margin-left: 48%;border:0px solid #ccc;background: #f2f2f2;">
            @foreach($language as $code => $lang)
                <option value="{{ $code }}" {{ session()->get('lang') == $code ? 'selected' : '' }}>{{ $lang }}</option>
            @endforeach
        </select>
    </div>
</div>
<!-- <div class="fot"><p>Copyright ©2014-2019 深圳易可达科技有限公司<br>粤ICP备16045411号-1</p></div> -->
<script>
    layui.use('form', function(){
        var form = layui.form;


    });
</script>
<script src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>
@include('layouts.language-js')
<script src="{{ asset('js/login.js?'.config('app.version')) }}"></script>
<script type="text/javascript" src="{{ asset('js/tool.js') }}"></script>
</body>
</html>
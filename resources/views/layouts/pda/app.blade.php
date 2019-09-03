<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>pda</title>

    @yield('css')

    <link rel="stylesheet" href="{{ asset('css/pda.css?'.config('app.version')) }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <link rel="stylesheet" href="{{ asset('layui/css/layui.css?'.config('app.version')) }}" media="all">
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
        window.timeZone = '{{ \App\Auth\Common\CurrentUser::getCurrentUser()->wareTime }}';
        var locale_message_language = "{{ session()->get('lang') ? session()->get('lang') : 'zh_CN' }}";
    </script>
</head>
<body>

@yield('content')


<script src="{{ asset('js/jquery-1.11.3.min.js?'.config('app.version')) }}"></script>
<script src="{{ asset('layui/layui.js?'.config('app.version')) }}"></script>
<script src="{{ asset('js/tool.js?'.config('app.version')) }}"></script>
@include('layouts.language-js')
@yield('javascripts')

</body>
</html>
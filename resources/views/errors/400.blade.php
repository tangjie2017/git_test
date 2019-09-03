<html>
<head>
    <title>400</title>
    <link href="{{ asset('css/error.css') }}" rel="stylesheet">
</head>
<body>
<div class="error_page">
    <div class="er_ico">
        <img src="{{ asset('img/error.png') }}" />
    </div>
    <div class="er_text">
        <h6>{{ isset($message) ? $message : 400 }}</h6>
        <p class="backline">
            <a class="backprev" href="javascript:history.go(-1)">{{ __('auth.returnToPreviousPage') }}</a>
        </p>
    </div>
</div>
</body>
</html>
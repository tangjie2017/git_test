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
        <h6>{{ isset($message) ? __('auth.HTTPCode500') : 500 }}</h6>
    </div>
</div>
</body>
</html>
@extends("layouts.app")
@section("content")
    <iframe id="authIframe" src="{{ $url }}?isBack=true" style="border:0;width:100%"></iframe>
    <script type="text/javascript">
        function reinitIframe(){
            var iframe = $("#authIframe")[0];
            try{
                var bHeight = iframe.contentWindow.document.body.scrollHeight;
                var dHeight = iframe.contentWindow.document.documentElement.scrollHeight;
                var height = Math.max(bHeight, dHeight);
                iframe.height = height;
            }catch (ex){
                console.log(iframe);
            }
        }
        window.setInterval(reinitIframe, 200);
    </script>
@endsection
@extends('layouts/app')

@section('content')
    <body  class="allbg" >
    <div class="bigbox1">
    <div class="titile">
        <div class="gclogo"><img src="{{asset('img/goodcang.png')}}" alt=""></div>
        <div class="clear"></div>
    </div>
    <div class="bespeaknum">
        <div class="yuyue songcang">
            <form action="">
                <h3><span>*</span>{{ __('auth.ReservationCode') }}：</h3><input type="text" name="code" id="code" value="{{ $reservationCode }}"><button type="button" id="subForm">{{ __('auth.Determine') }}</button>
            </form>
        </div>
        <div class="impor">
            <img src="{{asset('img/impor.png')}}" alt=""><h3>{{ __('auth.MattersAttention') }}：</h3>
            <p>{{__('auth.PromptInformation')}}</p>
        </div>
    </div>
    <!-- <div class="footcopy"><p>Copyright ©2014-2019 深圳易可达科技有限公司 粤ICP备16045411号-1</p></div> -->

</div>
{{--<div class="footte">Copyright ©2014-2019 深圳易可达科技有限公司 粤ICP备16045411号-1</div>--}}
@endsection

@section('javascripts')
<script>
    layui.use('form', function(){
        var form = layui.form,
            layer = layui.layer;


        $('#subForm').click(function () {
            var code = $('#code').val();
            $.MXAjax({
                type: "post",
                data: {
                    'code':code,
                    '_token':"<?php echo (csrf_token()); ?>" ,
                },
                dataType: 'json',
                url: "/reservation_code/create"+"?content="+"<?php echo Request::session()->get('content','')?>",

                success: function (response) {
                   if(response.Status==0){
                       layer.msg(response.Message);
                   }else{
                        window.location.href="{{url('reservation_code/UserAdd')}}"+"?id="+response.res['reservation_number_id']+"&content="+"<?php echo Request::session()->get('content','')?>";
                   }

                }
            });
        });
    });
</script>
    </body>
@endsection

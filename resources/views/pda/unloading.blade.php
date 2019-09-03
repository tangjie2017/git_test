@extends('layouts.pda.app')

@section('content')
    <div class="pdabg yuyueh5">
        <img src="{{ asset('img/bg.jpg') }}" alt="">
        <a href="{{ url('logout?redirect=/pda/login') }}"><div class="quit"><img src="{{ asset('img/quit.png') }}" alt=""></div></a>
        <a href="{{ url('pda/index') }}"><div class="in"><img src="{{ asset('img/home.png') }}" alt=""></div></a>
        <h5>{{ __('auth.Unloading') }}</h5>
        <div class="pdalogin yuyueform">
            <form action="" class="layui-form">
                <input type="hidden" name="cabinetId" class="cabinetId">
                <table class="layui-table layui-form" lay-skin="nob">
                    <tbody>
                    <tr><td>{{ __('auth.SingleNumber') }}：</td></tr>
                    <tr><td><input style="padding-left: 0; margin-top: 0;" type="text" name="reservationNum" class="reservationNum" maxlength="32" placeholder="{{ __('auth.SupportCabinetNumberReservationNumber') }}" autofocus  onkeypress="return inputNum(event)"></td></tr>
                    <tr><td>{{ __('auth.ActualUnloadingStartTime') }}：</td></tr>
                    <tr><td><input style="padding-left: 0; margin-top: 0;" type="text" name="actual_start_time" class="startTime" id="test1" placeholder="{{ __('auth.localCreateTime') }}" onkeypress="return inputStartTime(event)"></td></tr>
                    <tr><td>{{ __('auth.ActualUnloadingEndTime') }}：</td></tr>
                    <tr><td><input style="padding-left: 0; margin-top: 0;" type="text" name="actual_end_time" class="endTime" id="test2" placeholder="{{ __('auth.localCreateTime') }}"></td></tr>
                    </tbody>
                </table>
                <div class="establish noborder" style=" float: right; margin-right: 10px;">
                    <button type="button" class="layui-btn layui-btn-normal confirm" lay-submit="" lay-filter="searBtn" >{{ __('auth.Determine') }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('javascripts')
    <script>
        layui.use([ 'jquery', 'layer', 'laydate','form', 'element','upload' ], function() {
            var layer = layui.layer, form = layui.form, laydate = layui.laydate;
            var $ = layui.jquery;

            //确认预约完结
            form.on('submit(searBtn)',function(data){
                var field = data.field;
                if( !field.reservationNum){
                    layer.msg('{{ __('auth.SingleNumberRequired') }}',{icon :5})
                    return false;
                }
                if( !field.actual_start_time){
                    layer.msg('{{ __('auth.ActualUnloadingStartTimeRequired') }}',{icon :5})
                    return false;
                }
                if( !field.actual_end_time){
                    layer.msg('{{ __('auth.ActualUnloadingEndTimeRequired') }}',{icon :5})
                    return false;
                }
                if( field.actual_end_time < field.actual_start_time){
                    layer.msg('{{ __('auth.TheUnloadingCannotLessTheStartTime') }}',{icon :5})
                    return false;
                }
                $.MXAjax({
                    type: "post",
                    url: "/pda/unloading_submit",
                    dataType:"json",
                    data:{
                        '_token':"{{csrf_token()}}" ,
                        'info': data.field
                    },
                    success: function (response) {
                        //获取这个ID并移除
                        if (!response.Status){
                            layer.msg(response.Message, {icon:5}) ;
                        }else{
                            layer.msg(response.Message, {icon:6}) ;

                            setTimeout(function () {
                                $(".reservationNum").val('');
                                $(".startTime").val('');
                                $(".endTime").val('');
                                window.location.reload();
                            }, 2000);
                        }
                    }
                });
                return false;
            });

            //日期时间范围
            laydate.render({
                elem: '#test1'
                ,type: 'datetime'
            });
            laydate.render({
                elem: '#test2'
                ,type: 'datetime'
            });
        });

        //扫描预约单号或海柜号
        function inputNum(evt){
            evt = (evt) ? evt : ((window.event) ? window.event : "");
            if(evt.keyCode == 13) {
                var reservationNum = $.trim($('.reservationNum').val());
                var reg = new RegExp("^[0-9A-Za-z]*$");
                if (!reg.test(reservationNum)) {
                    layer.msg('{{ __('auth.ReservationMustBeNumberOrLetter') }}', {icon: 5});
                    return false;
                }

                if (reservationNum.length == 0) {
                    layer.msg('{{ __('auth.enterTheReservation') }}', {icon: 5});
                    return false;
                }
                $.MXAjax({
                    type: "get",
                    dataType: 'json',
                    url: "/pda/ajaxInputAppointmentNum/"+reservationNum,
                    data:{type:2},
                    success: function (res) {
                        if(res.Status) {
                            $(".cabinetId").val(res.Data);
                            $(".startTime").focus();
                            //获取当前时间
                            var myDate = new Date();
                            var year=myDate.getFullYear();   //获取当前年
                            var month=myDate.getMonth()+1;   //获取当前月
                            var date=myDate.getDate();       //获取当前日

                            var h=myDate.getHours();        //获取当前小时数(0-23)
                            var m=myDate.getMinutes();      //获取当前分钟数(0-59)
                            var s=myDate.getSeconds();

                            var now=year+'-'+getNow(month)+"-"+getNow(date)+" "+getNow(h)+':'+getNow(m)+":"+getNow(s);
                            $(".startTime").val(now);
                        }else{
                            $('.reservationNum').val('');
                            layer.msg(res.Message, {icon: 5});
                        }
                    },
                    error: function (e) {
                        layer.msg('{{ __('auth.systemIsAbnormalPleaseTryAgain') }}', {icon: 5});

                    }
                });
                return false;
            }
        }

        //实际卸货开始时间后面回车
        function inputStartTime(evt){
            evt = (evt) ? evt : ((window.event) ? window.event : "");
            if(evt.keyCode == 13) {

                $(".endTime").focus();
                //获取当前时间
                var myDate = new Date();
                var year=myDate.getFullYear();   //获取当前年
                var month=myDate.getMonth()+1;   //获取当前月
                var date=myDate.getDate();       //获取当前日

                var h=myDate.getHours();        //获取当前小时数(0-23)
                var m=myDate.getMinutes();      //获取当前分钟数(0-59)
                var s=myDate.getSeconds();

                var now=year+'-'+getNow(month)+"-"+getNow(date)+" "+getNow(h)+':'+getNow(m)+":"+getNow(s);
                $start = $(".startTime").val();
                if(now < $start){
                    layer.msg('{{ __('auth.TheUnloadingCannotLessTheStartTime') }}', {icon: 5});
                }
                $(".endTime").val(now);
            }


        }
        function getNow(s) {
            return s < 10 ? '0' + s: s;
        }
    </script>
@endsection

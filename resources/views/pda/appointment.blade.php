@extends('layouts.pda.app')


@section('content')
    <style>
        .layui-table img {
            max-width: 130px;
        }
    </style>
    <div class="pdabg yuyueh5">
        <img src="{{ asset('img/bg.jpg') }}" alt="">
        <a href="{{ url('logout?redirect=/pda/login') }}"><div class="quit"><img src="{{ asset('img/quit.png') }}" alt=""></div></a>
        <a href="{{ url('pda/index') }}"><div class="in"><img src="{{ asset('img/home.png') }}" alt=""></div></a>
        <h5>{{ __('auth.EndAppointment') }}</h5>
        <div class="pdalogin yuyueform">
            <form action="" class="layui-form">
                <input type="hidden" name="path" class="pathAll">
                <input type="hidden" class="num" value="">
                <input type="hidden" name="reservationId" class="reservationId">
                <table class="layui-table layui-form" lay-skin="nob">
                    <tbody>
                    <tr><td>{{ __('auth.SingleNumber') }}：</td></tr>
                    <tr><td><input style="padding-left: 0; margin-top: 0;" type="text" name="reservationNum" class="reservationNum" maxlength="32" placeholder="{{ __('auth.SupportCabinetNumberReservationNumber') }}" autofocus  onkeypress="return inputNum(event)"></td></tr>
                    <tr><td>{{ __('auth.ActualArrivalTime') }}：</td></tr>
                    <tr><td><input style="padding-left: 0; margin-top: 0;" type="text" name="actual_arrival_time" class="realTime" id="test10" placeholder="{{ __('auth.localCreateTime') }}"></td></tr>
                    <tr><td>{{ __('auth.photo') }}：</td></tr>
                    <tr><td>
                            <div class="layui-upload goodsUpIMG">
                                <button type="button" class="layui-btn" id="test1">{{ __('auth.uploadImage') }}</button>
                                <div id="div_prev" title=""></div>
                                <div id="prevModal"><img id="img_prev" style="max-width: none" /></div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="establish noborder" style=" float: right; margin-right: 10px;">
                    <button class="layui-btn layui-btn-normal confirm" lay-submit="" lay-filter="searBtn" >{{ __('auth.Determine') }}</button>
                </div>
            </form>
        </div>

     </div>
@endsection

@section('javascripts')
    <script>
        layui.use([ 'jquery', 'layer', 'laydate','form', 'element','upload' ], function() {
            var layer = layui.layer, form = layui.form, laydate = layui.laydate, upload = layui.upload;
            var $ = layui.jquery;

            //待提交的所有文件信息
            var files=[] ;


            //确认预约完结
            form.on('submit(searBtn)',function(data){
                var field = data.field;
                if( !field.reservationNum){
                    layer.msg('{{ __('auth.SingleNumberRequired') }}',{icon :5})
                    return false;
                }
                if( !field.actual_arrival_time){
                    layer.msg('{{ __('auth.ActualArrivalTimeRequired') }}',{icon :5})
                    return false;
                }
                if(files.length == 0){
                    layer.msg('{{ __('auth.PleaseUploadImage') }}',{icon :5})
                    return false;
                }
                if(files.length > 5){
                    layer.msg('{{ __('auth.UploadTo5Images') }}',{icon :5})
                    return false;
                }
                $.MXAjax({
                    type: "post",
                    url: "/pda/appointment_submit",
                    dataType:"json",
                    data:{
                        '_token':"{{csrf_token()}}" ,
                        'info': data.field,
                        'filesInfo' : files //图片
                    },
                    success: function (response) {
                        //获取这个ID并移除
                        if (!response.Status){
                            layer.msg(response.Message, {icon:5}) ;
                        }else{
                            layer.msg(response.Message, {icon:6}) ;
                            setTimeout(function () {
                                $(".reservationNum").val('');
                                $(".realTime").val('');
                                window.location.reload();
                            }, 2000);

                        }
                    }
                });
                return false;
            });

            //日期时间范围
            laydate.render({
                elem: '#test10'
                ,type: 'datetime'
            });

            //普通图片上传
            var i=1;

            upload.render({
                elem: '#test1'
                ,url: '/upload/file'
                ,multiple: true
                ,number: '5'
                ,accept: 'images'
                ,size: '5120'
                ,before: function(obj){
                }
                ,allDone: function(obj){
                    //图像预览，如果是多文件，会逐个添加。(不支持ie8/9)
                    layer.msg('{{ __('auth.UploadSuccess') }}' + obj.successful + ' ，{{__('auth.fail')}}' + obj.aborted, {icon: 6});
                    $('.confirm').attr("disabled",false);

                }
                ,choose: function(obj){
                    imgObjPre = obj ;
                    //如果添加图片，先将确认按钮设置disabled
                    $('.confirm').attr("disabled",true);
                }
                ,done: function(res){ //每个文件提交一次触发一次
                    if(res.Status){

                        var imgobj = new Image(); //创建新img对象
                        imgobj.src = res.Data.filePath; //指定数据源
                        imgobj.className = 'thumb';
                        imgobj.id= i;
                        i++;

                        var cret = document.createElement("div");
                        var del = document.createElement("span");
                        // var del = '<span class="delIcon"></span>';
                        cret.setAttribute("class","hoverbox aabb");
                        del.setAttribute("class","delIcon");



                        cret.appendChild(imgobj);
                        document.getElementById("div_prev").appendChild(cret); //添加到预览区域--
                        cret.appendChild(del);


                        // layer.msg('图片上传成功!' ,{icon:6}) ;
                        let re = files.push(res.Data.filePath);

                    }
                }
                ,error: function(e,x,t){
                    //请求异常回调
                }
            });

            //删除图片
            $(document).on("click",".delIcon",function(){
                let index = $(this).siblings('img').attr('id') ;
                if (index !== undefined) {
                    layer.msg('{{ __('auth.deleteSuccess') }}',{icon: 6}) ;
                    files.splice(index-1,1);
                }

                $(this).parent().remove();
            })
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
                    data:{type:1},
                    success: function (res) {
                        if(res.Status) {
                            $(".reservationId").val(res.Data);
                            // $(".realTime").focus();
                            //获取当前时间
                            var myDate = new Date();
                            var year=myDate.getFullYear();   //获取当前年
                            var month=myDate.getMonth()+1;   //获取当前月
                            var date=myDate.getDate();       //获取当前日

                            var h=myDate.getHours();        //获取当前小时数(0-23)
                            var m=myDate.getMinutes();      //获取当前分钟数(0-59)
                            var s=myDate.getSeconds();

                            var now=year+'-'+getNow(month)+"-"+getNow(date)+" "+getNow(h)+':'+getNow(m)+":"+getNow(s);
                            $(".realTime").val(now);
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

        function getNow(s) {
            return s < 10 ? '0' + s: s;
           }
        }
    </script>
@endsection


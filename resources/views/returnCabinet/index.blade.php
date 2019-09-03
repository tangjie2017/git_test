@extends('layouts/app')

{{--<style>--}}
    {{--.layui-table .laytable-cell-1-0-2{--}}
        {{--width: 300px;--}}
        {{--height:auto;--}}
        {{--overflow:visible;--}}
        {{--text-overflow:inherit;--}}
        {{--white-space:normal;--}}
    {{--}--}}
{{--</style>--}}
@section('content')
<div class="bgfff">
    <div class="container_full padd">
        <form class="layui-form" action="">
            <input id="returnCabinet_status" type="hidden" name="status" value="">
            <div class="tab1">
                <div class="col">
                    <h5>{{ __('auth.system') }}</h5>
                    <select name="system" >
                        <option value="">{{ __('auth.pleaseSelect') }}</option>
                        @foreach($system as $k => $value)
                            <option value="{{ $k }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <h5>{{ __('auth.warehouse') }}</h5>
                    <select name="warehouse" >
                        <option value="">{{ __('auth.pleaseSelect') }}</option>
                        <option value="{{ $wareCode }}">{{ $wareCode }}</option>
                    </select>
                </div>
                <div class="col">
                    <h5>{{ __('auth.CabinetType') }}</h5>
                    <select name="cabinet_type" >
                        <option value="">{{ __('auth.pleaseSelect') }}</option>
                        @foreach($cabinetType as $k => $value)
                            <option value="{{ $k }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <h5>{{ __('auth.source') }}</h5>
                    <select name="source" >
                        <option value="">{{ __('auth.pleaseSelect') }}</option>
                        @foreach($source as $k => $value)
                            <option value="{{ $k }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="clear"></div>
            </div>
            <div class="tab1 widthinput">
                <div class="col">
                    <h5>{{ __('auth.ReservationNumber') }}</h5>
                    <input type="text" name="reservation_number"/>
                </div>
                <div class="col">
                    <h5>{{ __('auth.ReservationCode') }}</h5>
                    <input type="text" name="reservation_code"/>
                </div>
                <div class="col">
                    <h5>{{ __('auth.TrackingNumber') }}</h5>
                    <input type="text" name="tracking_number"/>
                </div>
                <div class="col">
                    <h5>{{ __('auth.SeaCabinetNumber') }}</h5>
                    <input type="text" name="sea_cabinet_number"/>
                </div>
                <div class="clear"></div>
            </div>
            <div class="tab1 timechoos widthinput shijiwidth">
                <div class="col">
                    <h5>{{ __('auth.time') }}</h5>
                    <select name="time_type" >
                        <option value="1">{{ __('auth.ActualUnloadingStartTime') }}</option>
                        <option value="2">{{ __('auth.ActualUnloadingEndTime') }}</option>
                        <option value="3">{{ __('auth.NotifyReturnTime') }}</option>
                        <option value="4">{{ __('auth.ActualReturnTime') }}</option>
                        <option value="5">{{ __('auth.OperatingTime') }}</option>

                    </select>
                    <div class="layui-input-inline">
                        <input type="text" name="time_during" class="layui-input date-item" id="time1" >
                    </div>
                </div>
                <div class="col"><button class="layui-btn layui-btn-normal butwidth" lay-submit="" lay-filter="searBtn" id="submitForm">{{ __('auth.search') }}</button></div>
                <div class="clear"></div>
            </div>
            <div class="tab1 timechoos marginwidth nomargin">
                <div class="col">
                    <h5>{{ __('auth.status') }}</h5>
                    <div class="inputBlock">
                        <div class="multLable">
                            <em class="curr">{{ __('auth.all') }}</em>
                            @foreach($status as $k=>$v)
                                <em data-id="{{$k}}">{{ $v }}</em>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col flright">
                    <div class="righttext">
                        <h6>{{ __('auth.current') }}<span> </span>{{ __('auth.orders') }}</h6>
                        <div class="layui-btn layui-btn-normal butwidth exports" lay-submit=""  lay-filter="searBtn">{{ __('auth.Export') }}</div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </form>
        <div class="resulttab">
            <table class="layui-hide" id="demo" lay-filter="demo"></table>
        </div>

        {{--弹窗--}}
        <div class="lookyuyuetext" id="export" style="display:none;width:400px; height:80px; margin-top:20px;padding: 10px 20px">
            <table class="layui-table layui-form firrig" lay-skin="nob">
                <tbody>
                <tr>
                    <td>{{ __('auth.name') }}：</td>
                    <td><input type="text" class="layui-input" id="exports_name"></td>
                </tr>
                </tbody>
            </table>
        </div>

<script type="text/html" id="barDemo">
    @{{#  if(d.status === 1 || d.status === 4){ }}
    <button type="button" class="layui-btn layui-btn-xs" id="edit" lay-event="edit">{{ __('auth.view') }}</button>
    @{{#  } }}

    @{{#  if(d.status === 2 || d.status === 3){ }}
    <button type="button" class="layui-btn layui-btn-xs"  lay-event="edit">{{ __('auth.view') }}</button>
    <button type="button" class="layui-btn layui-btn-xs layui-btn-danger del" lay-event="del">{{ __('auth.MailDelivery') }}</button>
    @{{#  } }}
</script>
@endsection

@section('javascripts')
<script>

    layui.use(['form', 'laydate','table','element','laypage',], function(){
        var layer = layui.layer,
            form = layui.form,
            laypage = layui.laypage,
            laydate = layui.laydate;
        var element = layui.element;
        var $ = layui.jquery;
        var table = layui.table;

        var currPage = '';
        //监听行工具事件
        table.on('tool(demo)', function(obj){
            var data = obj.data;
            //console.log(obj)
            if(obj.event === 'del'){
                layer.open({
                    type: 2,
                    title: "{{ __('auth.MailDelivery') }}",
                    area: ['500px', '550px'],
                    scrollbar: false,
                    content: ["{{url('/return_cabinet/emiltext')}}"+"?id="+data.return_cabinet_id,'no'],
                    btn: ["{{__('auth.Determine')}}", "{{__('auth.Cancel')}}"],
                    yes: function(index){
                        $('del').attr('disabled',"true");//添加disabled属性
                        var info = window["layui-layer-iframe" + index].callbackdata();
                        var email = info.email;
                        var time = info.notice_return_time;
                        var reservation_number = data.rem.reservation_number;
                        $.ajax({
                            type: 'post',
                            data:{
                                email:email,
                                notice_return_time:time,
                                reservation_number:reservation_number,
                                _token:"<?php echo (csrf_token()); ?>"
                            },
                            dataType:'json',
                            url: '/return_cabinet/emil'+"?id="+data.return_cabinet_id,
                            success: function(response){
                                layer.msg(response.Message);
                                if(response.Status){
                                    layer.close(index);
                                    setTimeout(function(){  //使用  setTimeout（）方法设定定时2000毫秒
                                        showData();
                                    },1000);
                                }


                            }
                        })
                    }
                });
            } else if(obj.event === 'edit'){
                layer.open({
                    type: 2,
                    title: "{{ __('auth.view') }}",
                    shadeClose: true,//点击外围关闭弹窗
                    area: ['1050px', '800px'],
                    content: "{{url('/return_cabinet/look')}}"+"?id="+data.return_cabinet_id,
                });
            }
        });
        //日期时间范围
        lay('.date-item').each(function(){
            laydate.render({
                elem: this
                ,format:'yyyy-MM-dd HH:mm:ss'
                ,type:'datetime'
                ,trigger: 'click'
                ,range: true
            });
        });



        //搜索
        form.on('submit(searBtn)',function (data) {
            //展示数据
            t.reload({
                where: {data: data.field},
                page:{curr : 1}
            });
            return false;
        });

        //点击按钮查询
        $('.multLable em').click(function(){
            var ro = $(this).attr('data-id');
            $("#returnCabinet_status").val(ro);
            $("#submitForm").click();
        })


        //展示数据
        var t = table.render({

            elem: '#demo'
            ,url:'/return_cabinet/search'
            ,cols: [[ {type: 'checkbox',fixed: 'left'}
                ,{fixed: "left",width:200, title: '{{ __('auth.ReservationNumber') }}',templet:function (d) {
                    return d.rem.reservation_number;
                }}
                ,{fixed: 'left', title: '{{ __('auth.InboundOrderNumber') }}',width: 200,templet:function (d) {
                    var html='';
                    $.each(d.inbound,function (key,value) {
                        html += value.inbound_order_number + "<br>";
                    });
                    return html;

                }}
                ,{fixed: 'left', title: '{{ __('auth.TrackingNumber') }}',width: 150,templet:function (d) {
                    var html='';
                    $.each(d.inbound,function (key,value) {
                        if(value.tracking_number){
                            html += value.tracking_number + "<br>";
                        }
                    });
                    return html;

                }}
                ,{fixed: 'left', title: '{{ __('auth.SeaCabinetNumber') }}',width: 150,templet:function (d) {
                    var html='';
                    $.each(d.inbound,function (key,value) {
                        if(value.sea_cabinet_number){
                            html += value.sea_cabinet_number + "<br>";
                        }
                    });
                    return html;

                }}
                ,{field: 'system', title: '{{ __('auth.system') }}',templet:function (d) {
                    switch(d.system){
                        case 1:
                            return 'BIS';
                            break;
                        case 2:
                            return 'GC-OMS';
                            break;
                        case 3:
                            return 'EL-OMS';
                            break;
                        case 4:
                            return 'AE-OMS';
                            break;
                    }
                }}
                ,{field: 'warehouse_code', title: '{{ __('auth.warehouse') }}'}
                ,{field: 'customer_code', title: '{{ __('auth.CustomerCode') }}',templet:function (d) {
                    var arr = [];
                    $.each(d.inbound,function (key,value) {
                        arr.push(value.customer_code);
                    });
                    var new_arr = [];
                    for(var i=0;i<arr.length;i++) {
                        var items=arr[i];
                        //判断元素是否存在于new_arr中，如果不存在则插入到new_arr的最后
                        if($.inArray(items,new_arr)==-1) {
                            new_arr.push(items);
                        }
                    }
                    var html = '';
                    $.each(new_arr,function (key,value) {
                        html += value + "<br>"
                    });
                    return html;
                }}
                ,{field: 'cabinet_type', title: '{{ __('auth.CabinetType') }}',templet:function(d){
                    switch(d.cabinet_type){
                        case 1:
                            return '20GP';
                            break;
                        case 2:
                            return '40GP';
                            break;
                        case 3:
                            return '40HQ';
                            break;
                        case 4:
                            return '45HQ';
                            break;
                    }
                }}
                ,{field: 'actual_start_time', title: '{{ __('auth.ActualUnloadingStartTime') }}',hide: true,templet:function (d) {
                        if(d.actual_start_time != null) {
                            return d.actual_start_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                        }
                        return '';
                }}
                ,{field: 'actual_end_time', title: '{{ __('auth.ActualUnloadingEndTime') }}',hide: true, templet:function (d) {
                        if(d.actual_end_time != null) {
                            return d.actual_end_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                        }
                        return '';
                    }}
                ,{field: 'actual_return_time', title: '{{ __('auth.NotifyReturnTime') }}',hide: true, templet:function (d) {
                        if(d.actual_return_time != null) {
                            return d.actual_return_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                        }
                        return '';
                    }}
                ,{field: 'notice_return_time', title: '{{ __('auth.ActualReturnTime') }}',hide: true, templet:function (d) {
                        if(d.notice_return_time != null) {
                            return d.notice_return_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                        }
                        return '';
                    }}
                ,{field: 'operating_time', title: '{{ __('auth.OperatingTime') }}', templet:function (d) {
                        if(d.operating_time != null) {
                            return d.operating_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                        }
                        return '';
                    }}
                ,{field: 'operator', title: '{{ __('auth.Operator') }}'}
                ,{field: 'source', title: '{{ __('auth.source') }}',templet:function(d){
                    switch(d.source){
                        case 1:
                            return '{{ __('auth.client') }}';
                            break;
                        case 2:
                            return '{{ __('auth.warehouse') }}';
                            break;
                    }
                }}
                ,{field: 'status', title: '{{ __('auth.status') }}',templet:function(d){
                    switch(d.status){
                        case 1:
                            return '{{ __('auth.UnloadingCabinet') }}';
                            break;
                        case 2:
                            return '{{ __('auth.UnloadedCabinet') }}';
                            break;
                        case 3:
                            return '{{ __('auth.Cabinets') }}';
                            break;
                        case 4:
                            return '{{ __('auth.ReturnedCabinet') }}';
                            break;
                    }
                }}
                ,{field: 'right', title: '{{ __('auth.operation') }}',toolbar: '#barDemo',width:150 }
            ]]
            ,done:function (data,curr){
                $(".righttext span").text(data.count);
                currPage = curr;
            }
            ,even: true
            ,defaultToolbar:['filter']
            ,toolbar: true
            ,page :true
            ,limit: 10 //每页默认显示的数量
//            ,totalRow: true
        });

        function showData() {
            t.reload({
                page:{curr : currPage}
            });
        }

        //导出测试
        $(".exports").click(function () {
        form.on('submit(searBtn)',function(data){
            layer.open({
            type: 1,
            title: '{{ __('auth.Export') }}',
            shadeClose: true,//点击外围关闭弹窗
            area: ['400px', '200px'], 
            btn: ["{{__('auth.Determine')}}", "{{__('auth.Cancel')}}"],
            content:$('#export'),
            yes:function (index) {
                var exports_name = $('#exports_name').val();
                $.MXAjax({
                    type: "post",
                    url: "/return_cabinet/down",
                    data: {
                         'data':data.field,
                        'exports_name':exports_name,
                        '_token':"<?php echo (csrf_token()); ?>" ,
                    },
                    success: function (response) {
                            layer.msg(response.Message);
                            layer.close(index);
                    }
                });
            },
            end:function () {
                $('#exports_name').val('');
            }
            })


        });
        })


    });

</script>
@endsection
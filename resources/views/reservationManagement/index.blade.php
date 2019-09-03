@extends('layouts/dialog')

@section('content')
    <div class="bgfff">
        <div class="container_full padd">
            <form class="layui-form" action="">
                <div class="tab1">
                    <div class="col">
                        <h5>{{ __('auth.system') }}</h5>
                        <select name="system">
                            <option value="">{{ __('auth.pleaseSelect') }}</option>
                            @foreach($system as $k=> $value)
                                <option value="{{ $k }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <h5>{{ __('auth.warehouse') }}</h5>
                        <select name="warehouse">
                            <option value="">{{ __('auth.pleaseSelect') }}</option>
                            <option value="{{ $wareCode }}">{{ $wareCode }}</option>

                        </select>
                    </div>
                    <div class="col">
                        <h5>{{ __('auth.CabinetType') }}</h5>
                        <select name="cabinet_type">
                            <option value="">{{ __('auth.pleaseSelect') }}</option>
                            @foreach($cabinetType as $k=> $value)
                                <option value="{{ $k }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <h5>{{ __('auth.source') }}</h5>
                        <select name="source">
                            <option value="">{{ __('auth.pleaseSelect') }}</option>
                            @foreach($source as $k=> $value)
                                <option value="{{ $k }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col">
                        <h5>{{ __('auth.ReservationStatus') }}</h5>
                        <select name="reservation_status">
                            <option value="">{{ __('auth.pleaseSelect') }}</option>
                            @foreach($reservationStatus as $k=> $value)
                                <option value="{{ $k }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="tab1 widthinput">
                    <div class="col">
                        <h5>{{ __('auth.ReservationNumber') }}</h5>
                        <input type="text" name="reservation_number" id="reservation_number" value="{{ $reservation_number }}"/>
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
                <div class="tab1 widthinput shijiwidth">
                    <div class="col">
                        <h5>{{ __('auth.TheRemainingNumberDays') }}</h5>
                        <select name="remaining_type">
                            <option value="">{{ __('auth.pleaseSelect') }}</option>
                            <option value="1">></option>
                            <option value="2"><</option>
                            <option value="3">=</option>
                        </select>
                        <input type="text" name="remaining_time"/>
                    </div>
                    <div class="col">
                        <select name="time_type">
                            <option value="1">{{ __('auth.EarliestTime') }}</option>
                            <option value="2">{{ __('auth.LatestDeliveryTime') }}</option>
                            <option value="3">{{ __('auth.AppointmentDeliveryTime') }}</option>
                            <option value="4">{{ __('auth.ActualArrivalTime') }}</option>
                            <option value="5">{{ __('auth.CreationTime') }}</option>
                            <option value="6">{{ __('auth.UpdateTime') }}</option>
                        </select>
                        <div class="layui-input-inline">
                            <input type="text" name="time_day" class="layui-input" id="time1">
                        </div>
                    </div>
                    <input type="hidden" name="status" id="reservation_status" value="">
                    <div class="col"><button class="layui-btn layui-btn-normal butwidth" lay-submit="" lay-filter="searBtn" id="searbut">{{ __('auth.search') }}</button></div>
                    <div class="clear"></div>
                </div>
                <div class="tab1 marginwidth nomargin">
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
                            <h6>{{ __('auth.current') }}<span></span>{{ __('auth.orders') }}</h6>
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
                        <td>{{ __("auth.name") }}：</td>
                        <td><input type="text" class="layui-input" id="exports_name"></td>
                    </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection

@section('javascripts')
    <script type="text/html" id="barDemo">
        @{{#  if(d.status === 1 && d.reservation_status === 1){ }}
        <button type="button" class="layui-btn layui-btn-xs" lay-event="edit">{{ __('auth.edit') }}</button>
        <button type="button" class="layui-btn layui-btn-xs" lay-event="discard">{{ __('auth.Discard') }}</button>
        <button type="button" class="layui-btn layui-btn-xs" lay-event="appointmentReview">{{ __('auth.AppointmentReview') }}</button>
        @{{#  }else if(d.status === 3 && d.reservation_status === 1){ }}
        <button type="button" class="layui-btn layui-btn-xs" lay-event="review">{{ __('auth.Review') }}</button>
        <button type="button" class="layui-btn layui-btn-xs" lay-event="detail">{{ __('auth.view') }}</button>
        @{{#  }else{ }}
        <button type="button" class="layui-btn layui-btn-xs" lay-event="detail">{{ __('auth.view') }}</button>
        @{{# } }}

    </script>

    <script type="text/javascript" src="{{ asset('js/tab.js') }}"></script>
        <script>
        layui.use(['layer','form', 'laydate','table','element','laypage'], function(){
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

                if(obj.event === 'discard'){
                    layer.confirm('{{ __('auth.DiscardTheAppointmentForm') }}', function(index){
                        $.MXAjax({
                            type: 'POST',
                            url: '/reservation_management/discard',
                            data: {
                                '_token':"{{csrf_token()}}" ,
                                'id':data.reservation_number_id
                            },
                            dataType:  'json',
                            success: function(response){
                                if(response.Status){
                                    layer.msg(response.Message);
                                    setTimeout(function () {
                                        showData();
                                    },2000);

                                }else{
                                    layer.msg(response.Message);
                                }
                            },
                            error: function(e, x, d) {
                                layer.msg(d)
                            }
                        });
                    });
                } else if(obj.event === 'edit'){
                    window.location.href = "{{ url('reservation_management/edit') }}"+"?id="+data.reservation_number_id;
                }else if(obj.event === 'appointmentReview'){
                    layer.confirm("{{ __('auth.appointmentToReviewTheOrder') }}", function(index){
                        $.MXAjax({
                            type: 'POST',
                            url: '/reservation_management/appointmentReview',
                            data: {
                                '_token':"{{csrf_token()}}" ,
                                'id':data.reservation_number_id
                            },
                            dataType:  'json',
                            success: function(response){
                                if(response.Status){
                                    layer.msg(response.Message);
                                    setTimeout(function () {
                                        showData();
                                    },2000);

                                }else{
                                    layer.msg(response.Message);
                                }
                            },
                            error: function(e, x, d) {
                                layer.msg(d)
                            }
                        });
                    });
                }else if(obj.event === 'review'){
                    layer.open({
                        type: 2,
                        shadeClose: true,//点击外围关闭弹窗
                        title: "{{ __('auth.Review') }}",
                        area: ['1170px', '630px'],
                        content: ["{{ url('reservation_management/review') }}"+"?id="+data.reservation_number_id,'no'],
                        btn: ['{{ __('auth.Determine') }}', '{{ __('auth.Cancel') }}'],
                        yes: function (index, layero) {
                            var info = window["layui-layer-iframe" + index].callbackdata();
                            var id = info.id;
                            var appointment_delivery_time = info.appointment_delivery_time;
                            var contact_name = info.contact_name;
                            var telephone = info.telephone;
                            var email = info.email;
                            $.MXAjax({
                                type: "post",
                                data: {
                                    'id':id,
                                    'appointment_delivery_time':appointment_delivery_time,
                                    'contact_name':contact_name,
                                    'telephone':telephone,
                                    'email':email,
                                    '_token':"<?php echo (csrf_token()); ?>" ,
                                },
                                dataType: 'json',
                                url: "/reservation_management/updateReview"+"?content="+"<?php echo Request::session()->get('content','')?>",
                                success:function(response){
                                    layer.msg(response.Message);
                                    if(response.Status){
                                        setTimeout(function () {
                                            layer.close(index);
                                            showData();
                                        },2000);
                                    }
                                }
                            })
                        }

                    });
                }else if(obj.event === 'detail'){
                    layer.open({
                        type: 2,
                        shadeClose: true,//点击外围关闭弹窗
                        title: '{{ __('auth.view') }}',
                        area: ['1080px', '800px'],
                        content: ["{{ url('reservation_management/detail') }}"+"?id="+data.reservation_number_id,'no']                    });
                }
            });

            //日期时间范围
            laydate.render({
                elem: '#time1'
                ,type: 'datetime'
                ,range: true
            });

            //点击按钮查询
            $('.multLable em').click(function(){
                var status = $(this).attr('data-id');
                $("#reservation_status").val(status);
                $("#searbut").click();
            })

            //搜索
            $('#searbut').click(function () {
                form.on('submit(searBtn)',function (data) {
                    tableIns.reload({
                        where:{data:data.field},
                        page:{curr : 1}
                    });
                    return false;
                });
            });

            function showData(){
                tableIns.reload({
                    page:{curr : currPage}
                });
            }

            //展示已知数据
            var tableIns = table.render({
                toolbar: true,
                defaultToolbar:['filter'],
                elem: '#demo',
                where:{data:{'reservation_number':$('#reservation_number').val()}}
                ,url:'/reservation_management/search'
                ,cols: [[ {type: 'checkbox',fixed:'left'}
                    ,{fixed: 'left', title: '{{ __('auth.ReservationNumber') }}',width:160,templet: function(d){
                        return d.reservation_number;
                    }}
                    ,{fixed: 'left', title: '{{ __('auth.InboundOrderNumber') }}',width:150,templet:function (d) {
                        var html='';
                        $.each(d.inbound_order,function (key,value) {
                            if(value.inbound_order_number){
                                html += value.inbound_order_number + "<br>";
                            }
                        });
                        return html;
                    }}
                    ,{field: 'gzh', title: '{{ __('auth.TrackingNumber') }}',width:150,templet:function (d) {
                        var html='';
                        $.each(d.inbound_order,function (key,value) {
                            if(value.tracking_number){
                                html += value.tracking_number + "<br>";
                            }
                        });
                        return html;
                    }}
                    ,{field: 'hgh', title: '{{ __('auth.SeaCabinetNumber') }}',width:150,templet:function (d) {
                        var html='';
                        $.each(d.inbound_order,function (key,value) {
                            if(value.sea_cabinet_number){
                                html += value.sea_cabinet_number + "<br>";
                            }
                        });
                        return html;
                    }}
                    ,{field: 'reservation_code', title: '{{ __('auth.ReservationCode') }}',width:108,templet:function (d) {
                        if(d.reservation_code == null){
                            return '';
                        }
                        if(d.status == 2 ){
                        return '<a href="{{ url('reservation_code/UserIndex') }}?reservation_code='+d.reservation_code+'" style="color: #0379f7;" data-text="{{ __('auth.ReservationWarehouse') }}" data-bp="false" class="openTab" id="opentab">'+d.reservation_code+'</a>';
                        }else {
                            return d.reservation_code;
                        }

                    }}
                    ,{field: 'system', title: '{{ __('auth.system') }}',templet: function(d){
                        switch (d.system){
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
                        $.each(d.inbound_order,function (key,value) {
                            arr.push(value.customer_code);
                        });
                        var new_arr = [];
                        for(var i=0;i<arr.length;i++) {
                        　　var items=arr[i];
                        　　//判断元素是否存在于new_arr中，如果不存在则插入到new_arr的最后
                            if(items != ''){
                                if($.inArray(items,new_arr)==-1) {
                                    new_arr.push(items);
                                }
                            }　　
                        }
                        var html = '';
                        $.each(new_arr,function (key,value) {
                            html += value + "<br>"
                        });
                        return html;
                    }}
                    ,{field: 'cabinet_type', title: '{{ __('auth.CabinetType') }}',templet: function(d){
                        switch (d.cabinet_type){
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
                    ,{field: 'products_number', title: '{{ __('auth.NumberOfProducts') }}',templet:function (d) {
                        var products_number = 0;

                        $.each(d.inbound_order,function (key,value) {
                            products_number += parseInt(value.products_number);
                        });
                        return products_number;
                    }}
                    ,{field: 'earliest_delivery_time', title: '{{ __('auth.EarliestTime') }}',hide: true, templet:function (d) {
                            if(d.earliest_delivery_time != null) {
                                return d.earliest_delivery_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                            }
                            return '';
                        }}
                    ,{field: 'latest_delivery_time', title: '{{ __('auth.LatestDeliveryTime') }}',hide: true, templet:function (d) {
                            if(d.latest_delivery_time != null) {
                                return d.latest_delivery_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                            }
                            return '';
                        }}
                    ,{field: 'appointment_delivery_time', title: '{{ __('auth.AppointmentDeliveryTime') }}',hide: true, templet:function (d) {
                            if(d.appointment_delivery_time != null) {
                                return d.appointment_delivery_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                            }
                            return '';
                        }}
                    ,{field: 'actual_arrival_time', title: '{{ __('auth.ActualArrivalTime') }}',hide: true, templet:function (d) {
                            if(d.actual_arrival_time != null) {
                                return d.actual_arrival_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                            }
                            return '';
                        }}
                    ,{field: 'syts', title: '{{ __('auth.TheRemainingNumberDays') }}',hide: true,templet:function (d) {
                        if(d.status == 4){
                            return d.syts;
                        }else{
                            return '';
                        }
                    }}
                    ,{field: 'status', title: '{{ __('auth.status') }}',templet:function(d){
                        switch(d.status){
                            case 1:
                                return '{{ __('auth.draft') }}';
                                break;
                            case 2:
                                return '{{ __('auth.PendingAppointment') }}';
                                break;
                            case 3:
                                return '{{ __('auth.Pending') }}';
                                break;
                            case 4:
                                return '{{ __('auth.WaitingForDelivery') }}';
                                break;
                            case 5:
                                return '{{ __('auth.HasArrived') }}';
                                break;
                            case 6:
                                return '{{ __('auth.Discard') }}';
                                break;
                        }
                    }}
                    ,{field: 'reservation_status', title: '{{ __('auth.ReservationStatus') }}',templet:function(d){
                        switch(d.reservation_status){
                            case 1:
                                return '{{ __('auth.NotActive') }}';
                                break;
                            case 2:
                                return '{{ __('auth.Effective') }}';
                                break;
                            case 3:
                                return '{{ __('auth.expired') }}';
                                break;
                            case 4:
                                return '{{ __('auth.end') }}';
                                break;
                        }
                    }}
                    ,{field: 'reservation_num', title: '{{ __('auth.NumberOfAppointments') }}',hide: true}
                    ,{field: 'operating_time', title: '{{ __('auth.OperatingTime') }}',hide: true, templet:function (d) {
                                if(d.operating_time != null) {
                                    return d.operating_time.FormatToDate("yyyy-MM-dd hh:mm:ss");
                                }
                                return '';
                            }}
                    ,{field: 'operator', title: '{{ __('auth.Operator') }}',hide: true}
                    ,{field: 'source', title: '{{ __('auth.source') }}',width:80,templet:function(d){
                        switch(d.source){
                            case 1:
                                return '{{ __('auth.client') }}';
                                break;
                            case 2:
                                return '{{ __('auth.warehouse') }}';
                                break;
                        }
                    }}
                    ,{field: 'cz', title: '{{ __('auth.operation') }}',toolbar: '#barDemo',width:190}

                ]]
                ,done:function (data,curr){
                    $(".righttext span").text(data.count);
                    currPage = curr;
                    $('.openTab').tab(false);
                }
                ,even: true
                ,page: true //是否显示分页
                ,limit: 10 //每页默认显示的数量
            });


            //预约码弹窗
            window.dianji =  function(d) {
                layer.open({
                    type: 2,
                    shadeClose: true,//点击外围关闭弹窗
                    title: '{{ __('auth.view') }}',
                    area: ['1170px', '730px'],
                    content: ["{{ url('reservation_management/review') }}"+"?id="+d,'no'],
                    btn: ['{{ __('auth.Determine') }}', '{{ __('auth.Cancel') }}'],
                    yes:function (index,layero) {
                            var info = window["layui-layer-iframe" + index].callbackdata();
                            var id = info.id;
                            var appointment_delivery_time = info.appointment_delivery_time;
                            var contact_name = info.contact_name;
                            var telephone = info.telephone;
                            var email = info.email;
                            $.MXAjax({
                                type: "post",
                                data: {
                                    'id':id,
                                    'appointment_delivery_time':appointment_delivery_time,
                                    'contact_name':contact_name,
                                    'telephone':telephone,
                                    'email':email,
                                    '_token':"<?php echo (csrf_token()); ?>" ,
                                },
                                dataType: 'json',
                                url: "/reservation_code/update"+"?content="+"<?php echo Request::session()->get('content','')?>",
                                success:function(response){
                                    layer.msg(response.Message);
                                    if(response.Status){
                                        setTimeout(function () {
                                            layer.close(index);
                                            showData();
                                        },2000);
                                    }
                                }
                            })
                        }


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
                        btn:['{{ __('auth.Determine') }}','{{ __('auth.Cancel') }}'],
                        content:$('#export'),
                        yes:function (index) {
                            var exports_name = $('#exports_name').val();
                            $.MXAjax({
                                type: "post",
                                url: "/reservation_management/export",
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
                        },end:function () {
                            $('#exports_name').val('');
                        }
                    })


                });
            });

        });
    </script>
@endsection
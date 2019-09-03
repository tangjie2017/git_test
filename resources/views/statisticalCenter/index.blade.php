@extends('layouts/app')

@section('content')
<div class="bgfff">
    <div class="container_full padd">
        <form class="layui-form" action="">
            <div class="tab1 widthinput">
                <div class="col">
                    <h5>{{ __('auth.warehouse') }}</h5>
                    <select name="warehouse_code">
                        <option value="">{{__('auth.pleaseSelect')}}</option>
                        <option value="{{ $wareCode }}">{{ $wareCode }}</option>
                    </select>
                </div>
                <div class="col">
                    <h5>{{ __('auth.ReservationStatus') }}</h5>
                    <select name="reservation_status">
                        <option value="">{{__('auth.pleaseSelect')}}</option>
                        @foreach($reservationStatus as $k=> $value)
                            <option value="{{ $k }}">{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col">
                    <h5>{{ __('auth.CreationTime') }}</h5>
                    <input type="text" name="time_during" id="test10" >
                </div>
                <div class="col"><button class="layui-btn layui-btn-normal butwidth" lay-submit="" lay-filter="searBtn" id="submitForm">{{ __('auth.search') }}</button></div>
                <div class="clear"></div>
            </div>


            <div class="speedtc_col">
                <div class="st_panel2 kbin_tab">
                    <ul class="dataul">
                        <li><div class="iso"><h3>{{ __('auth.seaCabinetsNumber') }}</h3><p class="num reservation">0</p></div><a class="view" href="javascript:void(0);"></a></li>
                        <li><div class="iso"><h3>{{ __('auth.totalBoxNumber') }}</h3><p class="num box">0</p></div><a class="view" href="javascript:void(0);"></a></li>
                        <li class="notop"><div class="iso"><h3>{{ __('auth.SpeciesNumberOfSKU') }}</h3><p class="num sku">0</p></div><a class="view" href="javascript:void(0);"></a></li>
                        <li class="notop"><div class="iso"><h3>{{ __('auth.totalNumberOfSKU') }}</h3><p class="num product">0</p></div><a class="view" href="javascript:void(0);"></a></li>
                        <div class="clear"></div>
                    </ul>
                </div>
                <div class="st_panel kbin_tab">
                    <div class="kb_title schtitle">
                        <h2>{{ __('auth.seaCabinetsNumber') }}</h2>
                    </div>
                    <div class="st-Pbody STcartogram Orderment fclear">
                        <div class="item tim show">
                            <div class="areaStat" id="movement1" style="width:100%;height:320px"></div>
                        </div>

                    </div>
                </div>
            </div>

                <table class="layui-table textcenter" id="demo" lay-filter="demo"></table>

        </form>
    </div>
</div>

@endsection

@section('javascripts')
<script>
    layui.use(['form', 'laydate','table','element','laypage'], function(){
        var layer = layui.layer, form = layui.form, laydate = layui.laydate, table = layui.table;
        var $ = layui.jquery;

        //日期时间范围
        laydate.render({
            elem: '#test10'
            ,type: 'datetime'
            ,range: true
        });

        //删除
        $('#BatchDel').click(function(){
            var elect = $(this).parents('tr');
            layer.confirm('{{ __('auth.ConfirmDeleteOption') }}',{
                btn : [ '{{ __('auth.Determine') }}', '{{ __('auth.Cancel') }}' ],
                yes: function(index){
                    elect.remove();
                    layer.close(index);
                }
            })
        });

        //查询
        form.on('submit(searBtn)',function(data){
            table.render({
                elem:'#demo'
                ,url:'/statistical_center/list'
                ,where:{data:data.field}
                ,cols: [[
                    {field: 'warehouse_code', title: '{{ __("auth.warehouse") }}', templet:function(d){
                        return $.escapeHTML(d.warehouse_code);
                    }}
                    ,{field: 'cabinet', title: '{{ __("auth.CabinetType") }}'}
                    ,{field: 'reservation_num', title: '{{ __("auth.seaCabinetsNumber") }}'}
                    ,{field: 'sum_box', title: '{{ __("auth.totalBoxNumber") }}'}
                    ,{field: 'sum_product', title: '{{ __("auth.totalNumberOfSKU") }}'}
                    ,{field: 'sum_sku', title: '{{ __("auth.SpeciesNumberOfSKU") }}'}
                    ,{field: 'volume', title: '{{ __("auth.volume") }}', templet:function(d){
                        if(d.volume){
                            return parseFloat(d.volume).toFixed(2);
                        }
                        return 0;
                    }}
                    ,{field: 'reservation_status', title: '{{ __('auth.ReservationStatus') }}',templet:function(d){
                        switch (d.reservation_status){
                            case 1:
                                return '{{ __('auth.NotActive') }}';
                            case 2:
                                return '{{ __('auth.Effective') }}';
                            case 3:
                                return '{{ __('auth.expired') }}';
                            default:
                                return '{{ __('auth.end') }}';

                        }
                    }}
                    ,{field: 'dur_time', width:300, title: '时间',templet:function (d) {
                            var created_min,created_max = '';
                            if(d.created_min != null) {
                                created_min = d.created_min.FormatToDate("yyyy-MM-dd hh:mm:ss");
                            }

                            if(d.created_max != null) {
                                created_max = d.created_max.FormatToDate("yyyy-MM-dd hh:mm:ss");
                            }

                            return created_min + '~' + created_max;
                        }}
                ]]
                ,even: true
                ,page: true //是否显示分页
                ,limit: 10 //每页默认显示的数量
                ,done: function(res){
                    $(".reservation").text(res.reservation);
                    $(".box").text(res.box);
                    $(".sku").text(res.sku);
                    $(".product").text(res.product);

                    var dataBar = res.dataBar;
                    var timeBar = res.timeBar;
                    // console.log(dataBar);return false;
                    var chart = Highcharts.chart('movement1', {
                        chart: {
                            type: 'areaspline'
                        },
                        title: false,
                        xAxis: {
                            categories: JSON.parse(timeBar),
                            tickInterval: 1
                        },
                        yAxis: {
                            allowDecimals: false,
                            title: false
                        },
                        tooltip: {
                            shared: true,
                            crosshairs: true,
                            dateTimeLabelFormats: {
                                day: '%Y-%m-%d'
                            }
                        },
                        plotOptions: {
                            areaspline: {
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        // series: JSON.parse(dataBar)
                        series: [{
                            name: '{{ __("auth.seaCabinetsNumber") }}',
                            data: JSON.parse(dataBar)

                        }]
                    });


                }
            });

            return false;
        });

        //数据列表
        table.render({
            elem: '#demo'
            ,url:'/statistical_center/list'
            ,cols: [[
                {field: 'warehouse_code', title: '{{ __("auth.warehouse") }}'}
                ,{field: 'cabinet', title: '{{ __("auth.CabinetType") }}'}
                ,{field: 'reservation_num', title: '{{ __("auth.seaCabinetsNumber") }}'}
                ,{field: 'sum_box', title: '{{ __("auth.totalBoxNumber") }}'}
                ,{field: 'sum_product', title: '{{ __("auth.totalNumberOfSKU") }}'}
                ,{field: 'sum_sku', title: '{{ __("auth.SpeciesNumberOfSKU") }}'}
                ,{field: 'volume', title: '{{ __("auth.volume") }}', templet:function(d){
                        if(d.volume){
                            return parseFloat(d.volume).toFixed(2);
                        }
                        return 0;
                    }}
                ,{field: 'reservation_status', title: '{{ __('auth.ReservationStatus') }}',templet:function(d){
                        switch (d.reservation_status){
                            case 1:
                                return '{{ __('auth.NotActive') }}';
                            case 2:
                                return '{{ __('auth.Effective') }}';
                            case 3:
                                return '{{ __('auth.expired') }}';
                            default:
                                return '{{ __('auth.end') }}';

                        }
                    }}
                ,{field: 'dur_time', width:300, title: '{{ __("auth.time") }}',templet:function (d) {
                    var created_min,created_max = '';
                    if(d.created_min != null) {
                        created_min = d.created_min.FormatToDate("yyyy-MM-dd hh:mm:ss");
                    }

                    if(d.created_max != null) {
                        created_max = d.created_max.FormatToDate("yyyy-MM-dd hh:mm:ss");
                    }

                    return created_min + '~' + created_max;
                }}
            ]]
            ,even: true
            ,page: true //是否显示分页
            ,limit: 10 //每页默认显示的数量
            ,done: function(res){
                $(".reservation").text(res.reservation);
                $(".box").text(res.box);
                $(".sku").text(res.sku);
                $(".product").text(res.product);

                var dataBar = res.dataBar;
                var timeBar = res.timeBar;
                // console.log(dataBar);return false;
                var chart = Highcharts.chart('movement1', {
                    chart: {
                        type: 'areaspline'
                    },
                    title: false,
                    xAxis: {
                        categories: JSON.parse(timeBar),
                        tickInterval: 1
                    },
                    yAxis: {
                        allowDecimals: false,
                        title: false
                    },
                    tooltip: {
                        shared: true,
                        crosshairs: true,
                        dateTimeLabelFormats: {
                            day: '%Y-%m-%d'
                        }
                    },
                    plotOptions: {
                        areaspline: {
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    // series: JSON.parse(dataBar)
                    series: [{
                        name: '{{ __("auth.seaCabinetsNumber") }}',
                        data: JSON.parse(dataBar)

                    }]
                });


            }
        });

    });

</script>


@endsection
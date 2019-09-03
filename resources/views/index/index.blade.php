@extends('layouts/app')

@section('content')
    <div class="bgfff">
        <div class="container_full padd">
            <form class="layui-form" action="">
                <input type="hidden" name="dataBar" value="{{ $statistiacl['dataBar'] }}">
                <input type="hidden" name="timeBar" value="{{ $statistiacl['timeBar'] }}">
                <div class="speedtc_col">
                    <div class="st_panel2 kbin_tab">
                        <ul class="dataul">
                            <li><div class="iso"><h3>{{ __('auth.seaCabinetsNumber') }}</h3><p class="num reservation">{{ $statistiacl['reservation'] }}</p></div><a class="view" href="javascript:void(0);"></a></li>
                            <li><div class="iso"><h3>{{ __('auth.totalBoxNumber') }}</h3><p class="num box">{{ $statistiacl['box'] }}</p></div><a class="view" href="javascript:void(0);"></a></li>
                            <li class="notop"><div class="iso"><h3>{{ __('auth.SpeciesNumberOfSKU') }}</h3><p class="num sku">{{ $statistiacl['sku'] }}</p></div><a class="view" href="javascript:void(0);"></a></li>
                            <li class="notop"><div class="iso"><h3>{{ __('auth.totalNumberOfSKU') }}</h3><p class="num product">{{ $statistiacl['product'] }}</p></div><a class="view" href="javascript:void(0);"></a></li>
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
        var dataBar = $("input[name='dataBar']").val();
        var timeBar = $("input[name='timeBar']").val();
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
            series: [{
                name: '{{ __('auth.seaCabinetsNumber') }}',
                data: JSON.parse(dataBar)

            }]
        });

    </script>


@endsection
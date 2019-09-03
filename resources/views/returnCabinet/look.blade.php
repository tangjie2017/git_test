@extends('layouts/dialog')

@section('content')
    <style>
        body  {
            min-width: 1000px !important;
            padding: 10px 30px;
        }
    </style>
<div class="lookyuyuetext" id="lookyuyuetext" style="display: block; width:1000px;padding: 10px 20px;">
    <input type="hidden" class="id" name="id">
    <div class="lookyy">
        <h3>{{ __('auth.DocumentInformation') }}</h3>
        <table class="layui-table" lay-skin="nob">
            <tbody>
            <tr>
                <td>{{ __('auth.ReservationNumber') }}：{{ $res['rem']['reservation_number']}}</td>
                <td>{{ __('auth.InboundOrderNumber') }}：
                    @foreach($res['inbound'] as $v)
                        {{$v['inbound_order_number']}}
                    @endforeach
                </td>
            </tr>
            <tr>
                <td>{{ __('auth.TrackingNumber') }}：
                    @foreach($res['inbound'] as $v)
                        {{$v['tracking_number']}}
                    @endforeach
                </td>
                <td>{{ __('auth.SeaCabinetNumber') }} :
                    @foreach($res['inbound'] as $v)
                        {{$v['sea_cabinet_number']}}
                    @endforeach
                </td>
            </tr>
            <tr>
                <td colspan="2">{{ __('auth.status') }}：{{ \App\Services\ReturnCabinetService::getStatus($res['status']) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="lookyy latui-form">
        <h3>{{ __('auth.time') }}</h3>
        <table class="layui-table" lay-skin="nob">
            <tbody>
            <tr>
                <td>{{ __('auth.ActualUnloadingStartTime') }}：{{ \App\Models\Warehouse::switchTimeZone($res['actual_start_time']) }}</td>
                <td>{{ __('auth.ActualUnloadingEndTime') }}：{{ \App\Models\Warehouse::switchTimeZone($res['actual_end_time']) }}</td>
            </tr>
            <tr>
                <td>{{ __('auth.NotifyReturnTime') }}：{{ \App\Models\Warehouse::switchTimeZone($res['actual_return_time']) }}</td>
                <td>{{ __('auth.ActualReturnTime') }}：{{ \App\Models\Warehouse::switchTimeZone($res['notice_return_time']) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="lookyy">
        <h3>{{ __('auth.SupplierInformation') }}</h3>
        <table class="layui-table" lay-skin="nob">
            <tbody>
            <tr>
                <td style="width: 350px">{{ __('auth.userName') }}：{{$res['rem']['contact_name']}}</td>
                <td style="width: 350px">{{ __('auth.email') }}：{{$res['rem']['email']}}</td>
                <td>{{ __('auth.telPhone') }}：{{$res['rem']['telephone']}}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="lookyy borwid">
        {{--borwid--}}
        <div class="layui-tab">
            <ul class="layui-tab-title">
                <li class="layui-this">{{ __('auth.file') }}</li>
                <li>{{ __('auth.log') }}</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    @foreach($res['file'] as $k => $v)
                        <div class="colf" onclick = "previewImg({{ $k }})">
                            <img src="{{ $v['path'] }}" alt="" id="image{{$k}}" />
                        </div>
                    @endforeach
                </div>
                <div class="layui-tab-item textcenter">
                    <table class="layui-table" lay-skin="nob">
                        <thead>
                        <tr>
                            <th>{{ __('auth.Operator') }}</th>
                            <th>{{ __('auth.OperationType') }}</th>
                            <th>{{ __('auth.OperatingTime') }}</th>
                            <th>{{ __('auth.OperationalContent') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($res['log'] as $v)
                            <tr>
                                <td>{{ $v['operator'] }}</td>
                                <td>{{ $v['operation_type'] ==1 ? __('auth.add') :__('auth.edit')}}</td>
                                <td>{{ \App\Models\Warehouse::switchTimeZone($v['operating_time']) }}</td>
                                <td>{{ $v['content'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascripts')
<script>
    layui.use(['form', 'laydate','table','element','laypage'], function() {

        window.previewImg = function (src) {
            var default_config = {title: "{{ __('auth.PicturePreview') }}"};
            var image = '#image' + src;
            var img = new Image();
            img.src = $(image)[0].src;

            var max_height = $(window).height() - 100;
            var max_width = $(window).width();
            //rate1，rate2，rate3 三个比例中取最小的。
            var rate1 = max_height/img.height;
            var rate2 = max_width/img.width;
            var rate3 = 1;
            var rate = Math.min(rate1,rate2,rate3);
            //等比例缩放
            default_config.height = img.height * rate; //获取图片高度
            default_config.width = img.width  * rate; //获取图片宽度


            var imgHtml = "<img src='" + img.src + "' width='"+default_config.width+"px' height='"+default_config.height+"px' />";
            //弹出层
            layer.open({
                type: 1,
                shade: 0.8,
                offset: 'auto',
                area:[default_config.width+'px',default_config.height+50+'px'],
                shadeClose: true,//点击外围关闭弹窗
                scrollbar: false,//不现实滚动条
                title: "{{ __('auth.PicturePreview') }}", //不显示标题
                content: imgHtml, //捕获的元素，注意：最好该指定的元素要存放在body最外层，否则可能被其它的相对元素所影响
                cancel: function () {
                    //layer.msg('捕获就是从页面已经存在的元素上，包裹layer的结构', { time: 5000, icon: 6 });
                }

            });
        }
    })
</script>
@endsection



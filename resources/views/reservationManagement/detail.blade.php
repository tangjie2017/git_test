<style type="text/css">
    html,body{
        min-width: 1000px!important;
    }
</style>
@extends('layouts/dialog')

@section('content')
<div class="lookyuyuetext" style="display: block; width:1050px;height:500px;padding: 10px 20px;">
    <div class="lookyy">
        <h3>{{ __('auth.DocumentInformation') }}</h3>
        <table class="layui-table" lay-skin="nob">
            <tbody>
            <tr>
                <td>{{ __('auth.ReservationNumber') }}：{{ $data['reservation_number'] }}</td>
                <td>{{ __('auth.InboundOrderNumber') }}：@foreach($data['inbound_order'] as $v) {{ $v['inbound_order_number'] }}  @endforeach</td>
                <td>{{ __('auth.TrackingNumber') }}：@foreach($data['inbound_order'] as $v) {{ $v['tracking_number'] }}  @endforeach</td>
            </tr>
            <tr>
                <td>{{ __('auth.ReservationCode') }}：{{ $data['reservation_code'] }}</td>
                <td>{{ __('auth.status') }}：{{ \App\Services\ReservationManagementService::getStatus($data['status']) }}</td>
                <td>{{ __('auth.ReservationStatus') }}：{{ \App\Services\ReservationManagementService::getReservationStatus($data['reservation_status']) }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="lookyy">
        <h3>{{ __('auth.BasicInformation') }}</h3>
        <table class="layui-table" lay-skin="nob">
            <tbody>
            <tr>
                <td>{{ __('auth.system') }}：{{ \App\Services\ReservationManagementService::getSystem((int)$data['system']) }}</td>
                <td>{{ __('auth.DestinationWarehouse') }}：{{ \App\Services\ReservationManagementService::getWarehouse($data['warehouse_code']) }}</td>
                <td></td>
            </tr>
            <tr>
                <td>{{ __('auth.type') }}：{{ \App\Services\ReservationManagementService::getType($data['type']) }}</td>
                <td>{{ __('auth.CabinetType') }}：{{ \App\Services\ReservationManagementService::getCabinetType($data['cabinet_type']) }}</td>
                <td>{{ __('auth.ContainerType') }}:{{ \App\Services\ReservationManagementService::getContainerType($data['container_type']) }}</td>
            </tr>
            <tr>
                <td>{{ __('auth.CustomsClearanceTime') }}:{{ \App\Models\Warehouse::switchTimeZone($data['customs_clearance_time']) }}</td>
                <td>{{ __('auth.arrivalTime') }}:{{ \App\Models\Warehouse::switchTimeZone($data['arrival_time']) }}</td>
                <td>{{ __('auth.EarliestTime') }}：{{ \App\Models\Warehouse::switchTimeZone($data['earliest_delivery_time']) }}</td>
            </tr>
            <tr>
                <td>{{ __('auth.LatestDeliveryTime') }}：{{ \App\Models\Warehouse::switchTimeZone($data['latest_delivery_time']) }}</td>
                <td>{{ __('auth.ActualArrivalTime') }}：{{ \App\Models\Warehouse::switchTimeZone($data['actual_arrival_time']) }}</td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3">{{ __('auth.file') }}：@if($data['file'])
                        <a target="_blank" href="{{ url($data['file']) }}">
                            <?php if(substr($data['file'],strpos($data['file'],".")+1) == 'xls'){ ?>
                                <img src="{{ asset('img/xls.png') }}" style="width: 41px;height: 35px;" alt="">
                            <?php }else{?>
                                <img src="{{ asset('img/xlsx.png') }}" style="width: 41px;height: 35px;" alt="">
                            <?php }?>
                        </a>

                    @endif</td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="lookyy">
        <h3>{{ __('auth.SupplierInformation') }}</h3>
        <table class="layui-table" lay-skin="nob">
            <tbody>
            <tr>
                <td>{{ __('auth.ContactName') }}：{{ $data['contact_name'] }}</td>
                <td>{{ __('auth.email') }}：{{ $data['email'] }}</td>
                <td>{{ __('auth.phone') }}：{{ $data['telephone'] }}</td>
            </tr>
            </tbody>
        </table>
    </div>

    <div class="lookyy borwid">
        <div class="layui-tab">
            <ul class="layui-tab-title">
                <li class="layui-this">{{ __('auth.InboundOrderInformation') }}</li>
                <li>{{ __('auth.SignForPhoto') }}</li>
                <li>{{ __('auth.log') }}</li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show textcenter">
                    <table class="layui-table" lay-skin="nob">
                        <thead>
                        <tr>
                            <th>{{ __('auth.InboundOrderNumber') }}</th>
                            <th>{{ __('auth.TrackingNumber') }}</th>
                            <th>{{ __('auth.SeaCabinetNumber') }}</th>
                            <th>{{ __('auth.CustomerCode') }}</th>
                            <th>{{ __('auth.DestinationWarehouse') }}</th>
                            <th>{{ __('auth.NumberOfProducts') }}</th>
                            <th>{{ __('auth.NumberOfBoxes') }}</th>
                            <th>{{ __('auth.weight') }}（ KG ）</th>
                            <th>{{ __('auth.volume') }}（ cbm ）</th>
                            <th>{{ __('auth.CreationTime') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data['inbound_order'] as $v)
                            <tr>
                                <td>{{ $v['inbound_order_number'] }}</td>
                                <td>{{ $v['tracking_number'] }}</td>
                                <td>{{ $v['sea_cabinet_number'] }}</td>
                                <td>{{ $v['customer_code'] }}</td>
                                <td>{{ $v['warehouse_code'] }}</td>
                                <td>{{ $v['products_number'] }}</td>
                                <td>{{ $v['box_number'] }}</td>
                                <td>{{ $v['weight'] }}</td>
                                <td>{{ $v['volume'] }}</td>
                                <td>{{ \App\Models\Warehouse::switchTimeZone($v['created_at']) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="layui-tab-item">
                    @foreach($dataFile as $k=>$v)
                        <div class="colf" onclick = "previewImg({{ $k }})">
                            <img src="{{ $v['path'] }}" alt="" id="image{{$k}}"/>
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
                            <th width="30%">{{ __('auth.OperationalContent') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($dataLog as $v)
                            <tr>
                                <td>{{ $v['operator_user_name'] }}</td>
                                <td>{{ \App\Services\ReservationManagementService::getOperationType($v['operator_type']) }}</td>
                                <td>{{ \App\Models\Warehouse::switchTimeZone($v['operator_time']) }}</td>
                                <td style="word-wrap: break-word;word-break: normal;">{{ $v['content'] }}</td>
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
                var default_config = {title: "图片预览"};
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
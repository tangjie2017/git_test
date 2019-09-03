@extends('layouts/app')

@section('content')

<div class="bigbox"  style="background: #fff;  position: absolute;  border-radius: 5px; ">
    <div class="titile layui-form">
        <input type="hidden" name="reservation_number_id" value="{{$res['reservation_number_id']}}">
        {{--<div class="english">--}}
            {{--<select name="" id="">--}}
                {{--<option value="0">简体中文</option>--}}
                {{--<option value="1">ENGLISH</option>--}}
            {{--</select>--}}
        {{--</div>--}}
        <div class="clear"></div>
    </div>
    <div class="bespeaknum" style="height: 600px;">
        <form action="">
            <div class="bestext inputw">
                <h3>{{ __('auth.ReservationInformation') }}({{__('auth.localCreateTime')}})</h3>
                <table class="layui-table" lay-even lay-skin="nob">
                    <tbody>
                    <tr>
                        <td>{{ __('auth.ReservationNumber') }}：{{ $res['reservation_number'] }}</td>
                        <td></td>
                        <td>{{ __('auth.AppointmentDeliveryTime') }}</td>
                        <td><input type="text" name="appointment_delivery_time" value="{{ \App\Models\Warehouse::switchTimeZone($res['appointment_delivery_time']) }}" class="layui-input date-item" id="time1" value="" placeholder="{{ __('auth.WareHouseTime') }}" /></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="bestext inputw">
                <h3>{{ __('auth.SupplierInformation') }}</h3>
                <table class="layui-table" lay-even lay-skin="nob">
                    <tbody>
                    <tr>
                        <td>{{ __('auth.ContactName') }}：</td>
                        <td><input type="text" name="contact_name" value="{{ $res['contact_name'] }}"></td>
                        <td>{{ __('auth.phone') }}：</td>
                        <td><input type="text" name="telephone" value="{{ $res['telephone'] }}"></td>
                        <td>{{ __('auth.email') }}:</td>
                        <td><input type="text" name="email" value="{{ $res['email'] }}"></td>
                    </tbody>
                </table>
            </div>
            <div class="bestext inputw">
                <h3>{{ __('auth.ReserveProductInformation') }}</h3>
                <table class="layui-table" lay-skin="line">
                    <thead>
                    <tr>
                        <th>{{ __('auth.SerialNumber') }}</th>
                        <th>{{ __('auth.TrackingNumber') }}</th>
                        <th>{{ __('auth.SeaCabinetNumber') }}</th>
                        <th>{{ __('auth.InboundOrderNumber') }}</th>
                        <th>{{ __('auth.DestinationWarehouse') }}</th>
                        <th>{{ __('auth.NumberOfProducts') }}</th>
                        <th>{{ __('auth.NumberOfBoxes') }}</th>
                        <th>{{ __('auth.weight') }}（kg）</th>
                        <th>{{ __('auth.volume') }}（cbm）</th>
                        <th>{{ __('auth.createTime') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($res['InboundOrder']))
                    @foreach($res['InboundOrder'] as $k=> $v)
                    <tr>
                        <td>{{ $k+1 }}</td>
                        <td>{{ $v['tracking_number'] }}</td>
                        <td>{{ $v['sea_cabinet_number'] }}</td>
                        <td>{{ $v['inbound_order_number'] }}</td>
                        <td>{{ $v['warehouse_name'] }}</td>
                        <td>{{ $v['products_number'] }}</td>
                        <td>{{ $v['box_number'] }}</td>
                        <td>{{ $v['weight'] }}</td>
                        <td>{{ $v['volume'] }}</td>
                        <td>{{ \App\Models\Warehouse::switchTimeZone($v['created_at']) }}</td>
                    </tr>
                    @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection

@section('javascripts')

    <script>

        var callbackdata = function () {
            var data = {
                id : $('input[name = "reservation_number_id"]').val(),
                appointment_delivery_time : $('#time1').val(),
                contact_name : $('input[name = "contact_name"]').val(),
                telephone : $('input[name = "telephone"]').val(),
                email : $('input[name = "email"]').val(),
            };
            return data;
        };

        layui.use(['form','laydate'], function(){
            var form = layui.form,
                laydate = layui.laydate;
            //日期时间范围
            lay('.date-item').each(function(){
                laydate.render({
                    elem: this
                    ,format:'yyyy-MM-dd HH:mm:ss'
                    ,type:'datetime'
                    ,trigger: 'click'
                });
            });

        });
    </script>

@endsection



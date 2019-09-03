@extends('layouts/app')

@section('content')
<body class="allbg">
<div class="bigbox"  style="background: #fff; padding: 20px; position: absolute; top: 50%;   left: 50%; margin-top: -320px; margin-left: -590px; border-radius: 5px; ">
    <div class="titile">
        <input type="hidden" name="reservation_number_id" value="{{$res['reservation_number_id']}}">
        <div class="gclogo"><img src="{{asset('img/goodcang.png')}}" alt=""></div>
        <div class="clear"></div>
    </div>
    <div class="bespeaknum">
                <form action="">
                    <div class="bestext inputw">
                <h3>{{ __('auth.ReservationInformation') }}</h3>
                <table class="layui-table" lay-even lay-skin="nob">
                    <tbody>
                    <tr>
                        <td>{{ __('auth.ReservationNumber') }}：</td>
                        <td><input type="text" value="{{ $res['reservation_number'] }}" readonly></td>
                        <td>{{ __('auth.AppointmentDeliveryTime') }}：</td>
                        <td><input type="text" name="appointment_delivery_time" class="layui-input date-item" id="time1" placeholder="{{ __('auth.WareHouseTime') }}" /></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="bestext inputw">
                <h3>{{ __('auth.SupplierInformation') }}</h3>
                <table class="layui-table" lay-even lay-skin="nob">
                    <tbody>
                    <tr>
                        <td>{{ __('auth.SupplierInformation') }}：</td>
                        <td><input type="text" name="contact_name" value="{{ $res['contact_name'] }}"></td>
                        <td>{{ __('auth.phone') }}：</td>
                        <td><input type="text" name="telephone" value="{{ $res['telephone'] }}"></td>
                        <td>{{ __('auth.email') }}:</td>
                        <td><input type="text" name="email" value="{{ $res['email'] }}"></td>
                    </tr>
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
            <div class="bestext but"><button class="layui-btn layui-btn-normal save" type="button">{{ __('auth.Determine') }}</button><a id="a" class="layui-btn layui-btn-primary" href="">{{ __('auth.return') }}</a></div>
        </form>
    </div>
    <!-- <div class="footcopy"><p>Copyright ©2014-2019 深圳易可达科技有限公司 粤ICP备16045411号-1</p></div> -->
</div>
{{--<div class="footte">Copyright ©2014-2019 深圳易可达科技有限公司 粤ICP备16045411号-1</div>--}}
@endsection

@section('javascripts')

<script>

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


        //给a标签赋值
        document.getElementById("a").href="/reservation_code/UserIndex"+"?content="+"<?php echo Request::session()->get('content','')?>";

        $('.save').click(function (data) {
            var id = $('input[name = "reservation_number_id"]').val();
            var appointment_delivery_time = $('#time1').val();
            var contact_name = $('input[name = "contact_name"]').val();
            var telephone = $('input[name = "telephone"]').val();
            var email = $('input[name = "email"]').val();
//            console.log(id);return false;
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
                        $('.save').attr('disabled',"true");//添加disabled属性
                        setTimeout(function(){  //使用  setTimeout（）方法设定定时2000毫秒
                            window.location.href="{{url('reservation_code/UserIndex')}}?content="+"<?php echo Request::session()->get('content', '')?>";
                        },2000);

                    }
                }
        })
        });

    });
</script>
</body>
@endsection

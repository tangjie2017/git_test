@extends('layouts/app')

@section('content')
<div class="bgfff">
    <div class="container_full paddchuangjian">
        <div class="chuangjian">
            <h3>{{ __('auth.CreateAppointmentForm') }}</h3>
            <form action="" class="layui-form" id="myForm" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="establish">
                    <h5>{{ __('auth.BasicInformation') }}</h5>
                    <table  class="layui-table" lay-skin="nob">
                        <tbody>
                        <tr>
                            <td>{{ __('auth.system') }}</td>
                            <td><select name="system" id="system">
                                    <option value="">{{ __('auth.pleaseSelect') }}</option>
                                    @foreach($system as $k=> $value)
                                        <option value="{{ $k }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{ __('auth.DestinationWarehouse') }}</td>
                            <td><select name="warehouse_code" id="warehouse">
                                    <option value="">{{ __('auth.pleaseSelect') }}</option>
                                    <option value="{{ $wareCode }}">{{ $wareCode }}</option>

                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="establish">

                    <h5>{{ __('auth.ArrivalInformation') }}({{__('auth.EntryCorrespondingTimeLogin')}})</h5>
                    <table  class="layui-table" lay-skin="nob">
                        <tbody>
                        <tr>
                            <td>{{ __('auth.type') }}</td>
                            <td>
                                <input type="radio" name="type" value="1" title="{{ __('auth.TimeLimitedCabinet') }}">
                                <input type="radio" name="type" value="2" title="{{ __('auth.NonTimeLimitedCabinet') }}"></td>
                            <td>{{ __('auth.CabinetType') }}</td>
                            <td><select name="cabinet_type" id="cabinet_type">
                                    <option value="">{{__('auth.pleaseSelect')}}</option>
                                    @foreach($cabinetType as $k=> $value)
                                        <option value="{{ $k }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{{ __('auth.ContainerType') }}</td>
                            <td><select name="container_type" id="container_type">
                                    <option value="">{{__('auth.pleaseSelect')}}</option>
                                    @foreach($containerType as $k=> $value)
                                        <option value="{{ $k }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('auth.CustomsClearanceTime') }}</td>
                            <td><input type="text" name="customs_clearance_time" class="layui-input date-item" id="time1"></td>
                            <td>{{ __('auth.arrivalTime') }}</td>
                            <td><input type="text" name="arrival_time" class="layui-input date-item" id="time2"></td>
                            <td>{{ __('auth.EarliestTime') }}</td>
                            <td><input type="text" name="earliest_delivery_time" class="layui-input date-item" id="time3"></td>
                        </tr>
                        <tr>
                            <td>{{ __('auth.LatestDeliveryTime') }}</td>
                            <td><input type="text" name="latest_delivery_time" class="layui-input date-item" id="time4"></td>
                            <td>{{ __('auth.file') }}</td>
                            <td><button class="layui-btn layui-btn-normal" type="button" id="upload">{{ __('auth.SelectTheFile') }}</button>  <span id="tips">{{ __('auth.OnlySupported') }} xls、xlsx</span>
                                <input type="hidden" name="file" id="file" value=""/></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="establish">
                    <h5>{{ __('auth.FreightForwardingCompanyInformation') }}</h5>
                    <table  class="layui-table" lay-skin="nob">
                        <tbody>
                        <tr>
                            <td>{{ __('auth.ContactName') }}</td>
                            <td><input type="text" name="contact_name" id="contact_name"/></td>
                            <td>{{ __('auth.email') }}</td>
                            <td><input type="text" name="email" id="email"/></td>
                            <td>{{ __('auth.phone') }}</td>
                            <td><input type="text" name="telephone" id="telephone"/></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="establish establishTable">
                    <h5>{{ __('auth.InboundOrderInformation') }}</h5>

                        <table class="layui-table" lay-skin="nob">
                            <tbody>
                            <tr>
                                <td style="width: 130px !important;">{{ __('auth.InboundOrderNumber') }}</td>
                                <td><input type="text" name="inbound_order_number" id="inbound_order_number"/></td>
                                <td>{{ __('auth.TrackingNumber') }}</td>
                                <td><input type="text" name="tracking_number" id="tracking_number"/></td>
                                <td>{{ __('auth.SeaCabinetNumber') }}</td>
                                <td><input type="text" name="sea_cabinet_number" id="sea_cabinet_number"/></td>
                                <td><button type="button" class="layui-btn layui-btn-normal" id="djkbut" lay-filter="searBtn">{{ __('auth.search') }}</button></td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>

                    <div class="resulttab">
                    <table class="layui-table textcenter" id="demo" lay-filter="demo"></table>
                        <input type="hidden" name="inbound_order_info" id="inb">
                    </div>

                </div>
                <div class="establish noborder">
                    <button type="button" class="layui-btn layui-btn-normal save">{{ __('auth.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="layui-layer-shade" id="layui-layer-shade-success" times="1" style="z-index: 19891014; background-color: rgb(0, 0, 0); opacity: 0.3;display: none;"></div>
<div class="layui-layer layui-layer-dialog" id="layui-layer-success" type="dialog" times="1" showtime="0" contype="string" style="display: none;z-index: 19891015; top: 125px; left: 670px;">
    <div class="layui-layer-title" style="cursor: move;">{{ __('auth.information') }}</div>
    <div id="menu-list" class="layui-layer-content">{{ __('auth.CreateSuccessfully') }}，{{ __('auth.YourReservationNumber') }}:<a href="" data-bp="false" class="openTab" data-text="{{ __('auth.ReservationManagement') }}"  id="opentab"></a></div>
    <span class="layui-layer-setwin"></span>
    <div class="layui-layer-btn layui-layer-btn-">
        <a class="layui-layer-btn0" id="success-close">{{ __('auth.Determine') }}</a>
    </div>
    <span class="layui-layer-resize"></span>
</div>
@endsection

@section('javascripts')
    <script type="text/javascript" src="{{ asset('js/tab.js?'.config('app.version')) }}"></script>
<script>
    $(function () {
        $('.openTab').tab(false);
        $('#success-close').click(function () {
            $('#layui-layer-shade-success').hide();
            $('#layui-layer-success').hide();
            window.location.reload();
        })
    });

    layui.use(['form', 'laydate','table','element','laypage','upload'], function(){
        var layer = layui.layer,
            form = layui.form,
            laypage = layui.laypage,
            laydate = layui.laydate;
        var element = layui.element;
        var upload = layui.upload;
        var table = layui.table;
        var $ = layui.jquery;

        //日期时间范围
        lay('.date-item').each(function(){
            laydate.render({
                elem: this
                ,format:'yyyy-MM-dd HH:mm:ss'
                ,type:'datetime'
                ,trigger: 'click'
            });
        });

        var ids = [];

        //保存
        $('.save').click(function () {
            var self = $(this);
            self.attr('disabled', true);
            var re = ids;

//            var arr = [];  //定义一个临时数组
//            for(var i = 0; i < ids.length; i++){  //循环遍历当前数组
//                //判断当前数组下标为i的元素是否已经保存到临时数组
//                //如果已保存，则跳过，否则将此元素保存到临时数组中
//                if(arr.indexOf(ids[i]) == -1){
//                    arr.push(ids[i]);
//                }
//            }
//            return arr;

            var container_type = $('#container_type').val();
            if(container_type == 3 || container_type ==4){
                if($('#file').val() == ''){
                    layer.msg('{{ __('auth.PleaseUploadAttachments') }}');
                    self.removeAttr('disabled');
                    return false;
                }

            }

            if(re == ''){
                layer.msg('{{ __('auth.PleaseSelectTheInboundOrderInformation') }}');
                self.removeAttr('disabled');
                return false;
            }
            $('#inb').val(JSON.stringify(re));

            $.MXAjax({
                type:'POST',
                dataType:'json',
                url:'{{ url('/reservation_management/addOrUpdate') }}',
                data: $('#myForm').serialize(),
                success:function (response) {
                    if (response.code == 0) {
                        $('#opentab').text(response.data);
                        $('#opentab').attr('href', "{{ url('reservation_management/index?reservation_number=') }}"+response.data);
                        $('#layui-layer-shade-success').show();
                        $('#layui-layer-success').show();

                    } else {
                        layer.msg(response.msg);
                        self.removeAttr('disabled');
                    }
                },
                error: function (e) {
                    layer.msg(e.message);
                    self.removeAttr('disabled');
                }
            })
        });


        var tableIns = table.render({
            elem: '#demo'
            ,cols: [[
                {type: 'checkbox'}
                ,{field: 'inbound_order_number', title: '{{ __('auth.InboundOrderNumber') }}',width:180}
                ,{field: 'tracking_number', title: '{{ __('auth.TrackingNumber') }}',width:100}
                ,{field: 'sea_cabinet_number', title: '{{ __('auth.SeaCabinetNumber') }}',width:100}
                ,{field: 'customer_code', title: '{{ __('auth.CustomerCode') }}' ,width:100}
                ,{field: 'warehouse_code', title: '{{ __('auth.warehouseCode') }}' ,width:100}
                ,{field: 'warehouse_name', title: '{{ __('auth.DestinationWarehouse') }}' ,hide:true}
                ,{field: 'products_number', title: '{{ __('auth.NumberOfProducts') }}' ,width:100}
                ,{field: 'box_number', title: '{{ __('auth.NumberOfBoxes') }}' ,width:100}
                ,{field: 'sku_species_number', title: '{{ __('auth.SpeciesNumberOfSKU') }}' ,width:100}
                ,{field: 'weight', title: '{{ __('auth.weight') }}' ,width:100}
                ,{field: 'volume', title: '{{ __('auth.volume') }}' ,width:100}
                ,{field: 'created_at', title: '{{ __('auth.CreationTime') }}' ,width:180,templet:function (d) {
                    if(d.created_at != null) {
                        return d.created_at.FormatToDate("yyyy-MM-dd hh:mm:ss");
                    }
                    return '';
                }}
            ]]
            ,data:[]
            ,even: true
            ,page: true //是否显示分页
            ,limit: 10 //每页默认显示的数量

        });
        var totalRecord = table.init.length;
        var page = $(".layui-laypage-em").next().html(); //当前页码值

        if(page){
            page = page;
        }else{
            page =1;
        }

        var currPageNo = Math.ceil(totalRecord / page);

        //调接口按钮
        $('#djkbut').click(function () {
            var self = $(this);
            self.attr('disabled', true);
            var tracking_number = $('#tracking_number').val();
            var inbound_order_number = $('#inbound_order_number').val();
            var warehouse = $('#warehouse').val();
            var sea_cabinet_number = $('#sea_cabinet_number').val();

            if(tracking_number == '' && inbound_order_number == '' && sea_cabinet_number == ''){
                layer.msg('{{ __('auth.PleaseFillTheQueryConditions') }}');
                setTimeout(function () {
                    tableIns.reload({
                        data : []
                    });
                },2000);

                self.removeAttr('disabled');
                return false;
            }

            if(warehouse == ''){
                layer.msg('{{ __('auth.selectTheWarehouse') }}');
                self.removeAttr('disabled');
                return false;
            }

            $.MXAjax({
                type:'post',
                url:'/reservation_management/searchInbound',
                data: {inbound_order_number:inbound_order_number,tracking_number:tracking_number,warehouse_code:warehouse,sea_cabinet_number:sea_cabinet_number},
                dataType:'json',
                success:function (res) {
                    if (res.code == 0) {
                        $('#tracking_number').val('');
                        $('#inbound_order_number').val('');
                        $('#sea_cabinet_number').val('');

                        tableIns.reload({
                            data : res.data
                        });
                        self.removeAttr('disabled');
                    }else if(res.code == 404){
                        layer.msg(res.msg);
                        tableIns.reload({
                            data : []
                        });
                        self.removeAttr('disabled');
                    }
                },
                error: function (e) {
                    layer.msg(e.message);
                    self.removeAttr('disabled');
                }
            })
        });

        //上传附件
        var uploadInst = upload.render({
            elem:"#upload"
            ,accept: 'file'
            ,size: '1024*10'
            ,exts:"xls|xlsx"
            ,url: "/reservation_management/upload"
            ,done: function(res){ //上传后的回调
                if(res.Status){
                    $('#tips').html(res.Data.name);
                    $('#file').val(res.Data.filePath);
                    layer.msg('{{ __('auth.UploadSuccess') }}');
                }else{
                    layer.msg(res.Message);
                }
            }
        });

        //复选框选中监听,将选中的id 设置到缓存数组,或者删除缓存数组
        table.on('checkbox(demo)', function (obj) {
            var checkStatus = table.checkStatus('demo');
            if(checkStatus.isAll){
                $(' .layui-table-header th[data-field="0"] input[type="checkbox"]').prop('checked', true);
                $('.layui-table-header th[data-field="0"] input[type="checkbox"]').next().addClass('layui-form-checked');
            }

            if(obj.checked==true){
                if(obj.type=='one'){
                    ids.push(obj.data);
                }else{
                    for(var i=0;i<checkStatus.data.length;i++){
                        ids.push(checkStatus.data[i]);
                    }
                }
            }else{
                if(obj.type=='one'){
                    for(var i=0;i<ids.length;i++){
                        if(ids[i].inbound_order_number == obj.data.inbound_order_number){
                            ids.splice(i,1);
                        }
                    }
                }else{
                    for(var i=0;i<ids.length;i++){
                        for(var j=0;j<checkStatus.data.length;j++){
                            if(ids[i]==checkStatus.data[j]){
                                ids.splice(i,1);
                            }
                        }
                    }
                }
            }
        });



    });
</script>
@endsection
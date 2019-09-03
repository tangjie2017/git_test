@extends('layouts/app')

@section('content')
    <div class="bgfff">
        <div class="container_full paddchuangjian">
    <div class="chuangjian">
        <h3>{{ __('auth.edit') }}</h3>
        <form action="" class="layui-form" id="myForm" method="post" enctype="multipart/form-data" >
            {{ csrf_field() }}
            <div class="establish">
                <h5>{{ __('auth.BasicInformation') }}</h5>
                <input type="hidden" name="reservation_number_id" id="reservation_number_id" value="{{ $data['reservation_number_id'] }}">
                <table  class="layui-table" lay-skin="nob">
                    <tbody>
                    <tr>
                        <td>{{ __('auth.system') }}</td>
                        <td><select name="system" id="system">
                                @foreach($system as $k=> $value)
                                    <option value="{{ $k }}" {{ $k == $data['system'] ? 'selected' : ''}} >{{ $value }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{ __('auth.DestinationWarehouse') }}</td>
                        <td><select name="warehouse_code" id="warehouse">
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
                            <input type="radio" name="type" value="1" {{ $data['type'] == 1 ? 'checked' : ''}} title="{{ __('auth.TimeLimitedCabinet') }}">
                            <input type="radio" name="type" value="2" {{ $data['type'] == 2 ? 'checked' : ''}} title="{{ __('auth.NonTimeLimitedCabinet') }}"></td>
                        <td>{{ __('auth.CabinetType') }}</td>
                        <td><select name="cabinet_type" id="cabinet_type">
                                @foreach($cabinetType as $k=> $value)
                                    <option value="{{ $k }}" {{ $k == $data['cabinet_type'] ? 'selected' : ''}} >{{ $value }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{ __('auth.ContainerType') }}</td>
                        <td><select name="container_type" id="container_type">
                                @foreach($containerType as $k=> $value)
                                    <option value="{{ $k }}" {{ $k == $data['container_type'] ? 'selected' : ''}} >{{ $value }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('auth.CustomsClearanceTime') }}</td>
                        <td>
                            <input type="text" name="customs_clearance_time" class="layui-input date-item" value="{{ \App\Models\Warehouse::switchTimeZone($data['customs_clearance_time']) }}">
                        </td>
                        <td>{{ __('auth.arrivalTime') }}</td>
                        <td><input type="text" name="arrival_time" class="layui-input date-item" value="{{ \App\Models\Warehouse::switchTimeZone($data['arrival_time']) }}"></td>
                        <td>{{ __('auth.EarliestTime') }}</td>
                        <td><input type="text" name="earliest_delivery_time" class="layui-input date-item" value="{{ \App\Models\Warehouse::switchTimeZone($data['earliest_delivery_time']) }}"></td>
                    </tr>
                    <tr>
                        <td>{{ __('auth.LatestDeliveryTime') }}</td>
                        <td><input type="text" name="latest_delivery_time" class="layui-input date-item" value="{{ \App\Models\Warehouse::switchTimeZone($data['latest_delivery_time']) }}"></td>
                        <td>{{ __('auth.file') }}</td>
                        <td><button class="layui-btn layui-btn-normal" type="button" id="upload">{{ __('auth.SelectTheFile') }}</button>
                            <span id="tips">
                                @if($data['file'])
                                    <a target="_blank" href="{{ url($data['file']) }}">
                                    <?php if(substr($data['file'],strripos($data['file'],".")+1) == 'xls'){ ?>
                                        <img src="{{ asset('img/xls.png') }}" style="width: 41px;height: 35px;" alt="">
                                    <?php }else{?>
                                        <img src="{{ asset('img/xlsx.png') }}" style="width: 41px;height: 35px;" alt="">
                                    <?php }?>
                                    </a>
                                @else
                                    {{ __('auth.OnlySupported') }}xls、xlsx
                                @endif
                            </span>
                            <input type="hidden" name="file" id="file" value="{{ $data['file'] }}"/></td>
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
                        <td><input type="text" name="contact_name" id="contact_name" value="{{ $data['contact_name'] }}"/></td>
                        <td>{{ __('auth.email') }}</td>
                        <td><input type="text" name="email" id="email" value="{{ $data['email'] }}"/></td>
                        <td>{{ __('auth.phone') }}</td>
                        <td><input type="text" name="telephone" id="telephone" value="{{ $data['telephone'] }}"/></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="establish establishTable">
                <h5>{{ __('auth.InboundOrderInformation') }}</h5>
                <table  class="layui-table" lay-skin="nob">
                    <tbody>
                    <tr>
                        <td style="width: 130px !important;">{{ __('auth.InboundOrderNumber') }}</td>
                        <td><input type="text" id="inbound_order_number"/></td>
                        <td>{{ __('auth.TrackingNumber') }}</td>
                        <td><input type="text" id="tracking_number"/></td>
                        <td>{{ __('auth.SeaCabinetNumber') }}</td>
                        <td><input type="text" id="sea_cabinet_number"/></td>
                        <td><button class="layui-btn layui-btn-normal" id="djkbut">{{ __('auth.search') }}</button></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>

                <div class="resulttab">
                    <span id="table1"><table class="layui-table textcenter demo" id="demo" lay-filter="demo"></table></span>
                    <table class="layui-table textcenter" id="demo1" lay-filter="demo1"></table>
                    <input type="hidden" name="inbound_order_info" id="inb">
                </div>

            </div>
            <div class="establish noborder">
                <button type="button" class="layui-btn layui-btn-normal save">{{ __('auth.save') }}</button>
                <button type="button" class="layui-btn layui-btn-danger cancel">{{ __('auth.Cancel') }}</button>
            </div>
        </form>
    </div>
        </div>
    </div>

@endsection

@section('javascripts')
    <script>

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

            var reservation_number_id = $('#reservation_number_id').val();
            var info = [];
            var table1 = table.render({
                elem: '#demo'
                ,where:{id:reservation_number_id}
                ,url:'/reservation_management/getEditInboundOrder'
                ,cols: [[
                    {type: 'checkbox'/*,width:150*/,"LAY_CHECKED":true}
                    ,{field: 'inbound_order_number', title: '{{ __('auth.InboundOrderNumber') }}',width:180}
                    ,{field: 'tracking_number', title: '{{ __('auth.TrackingNumber') }}',width:100}
                    ,{field: 'sea_cabinet_number', title: '{{ __('auth.SeaCabinetNumber') }}',width:100}
                    ,{field: 'customer_code', title: '{{ __('auth.CustomerCode') }}' ,width:100}
                    ,{field: 'warehouse_code', title: '{{ __('auth.warehouseCode') }}',width:100}
                    ,{field: 'warehouse_name', title: '{{ __('auth.DestinationWarehouse') }}' ,hide:true }
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
                ,done:function (data,curr){
                    info = data.data;
                }
                ,even: true
                ,page: true //是否显示分页
                ,limit: 10 //每页默认显示的数量

            });

            $('.cancel').click(function () {
               window.location.href = "{{ url('reservation_management/index') }}" ;
            });

            //保存
            $('.save').click(function () {
                var self = $(this);
                self.attr('disabled', true);

                var ids3 = [];
                $.each(info,function (key,value) {
                    if(value.LAY_CHECKED == true){
                        ids3.push(value);
                    }
                });

                var re = Object.assign(ids1,ids2,ids3);
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
                    dataType:'JSON',
                    url:'{{ url('/reservation_management/addOrUpdate') }}',
                    data: $('#myForm').serialize(),
                    success:function (response) {
                        if (response.code == 0) {
                            layer.msg(response.msg);
                            setTimeout(function () {
                                window.location.href = "{{ url('reservation_management/index') }}" ;
                            }, 2000);

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

//            var oldData =  tableIns.cache["demo"];
//            var totalRecord = table.init.length;
//            var page = $(".layui-laypage-em").next().html(); //当前页码值
//
//            if(page){
//                page = page;
//            }else{
//                page =1;
//            }
//
//            var currPageNo = Math.ceil(totalRecord / page);

            var ids1 = [];
            var ids2 = [];
            var oldData =[];

            //调接口按钮
            $('#djkbut').click(function () {
                var self = $(this);
                self.attr('disabled', true);
                var tracking_number = $('#tracking_number').val();
                var inbound_order_number = $('#inbound_order_number').val();
                var sea_cabinet_number = $('#sea_cabinet_number').val();
                var reservation_number_id = $('#reservation_number_id').val();

                if(tracking_number == '' && inbound_order_number == '' && sea_cabinet_number == ''){
                    layer.msg('{{ __('auth.PleaseFillTheQueryConditions') }}');

                    setTimeout(function () {
                        table1.reload({

                        });
                    },2000);


                    self.removeAttr('disabled');
                    return false;
                }

                var warehouse = $('#warehouse').val();
                if(warehouse == ''){
                    layer.msg('{{ __('auth.selectTheWarehouse') }}');
                    self.removeAttr('disabled');
                    return false;
                }

                $.MXAjax({
                    type:'post',
                    url:'/reservation_management/searchInbound',
                    data: {
                        inbound_order_number:inbound_order_number,
                        tracking_number:tracking_number,
                        warehouse_code:warehouse,
                        sea_cabinet_number:sea_cabinet_number,
//                        reservation_number_id:reservation_number_id
                    },
                    dataType:'json',
                    success:function (res) {
                        if (res.code == 0) {
//                            oldData =  table.cache["demo"];
//                            var newdata = oldData.concat(res.data);
//                            $.each(res.data,function (key,value) {
//                                oldData.push(value);
//                            });

                            $('#tracking_number').val('');
                            $('#inbound_order_number').val('');
                            $('#sea_cabinet_number').val('');

                            $('#table1').remove();

                            table.render({
                                elem: '#demo1'
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
                                    ,{field: 'created_at', title: '{{ __('auth.CreationTime') }}' ,width:180}
                                ]]
                                ,data:res.data
                                ,done:function (data,curr){
                                    info = res.data;
                                }
                                ,even: true
                                ,page: true
                                ,limit: 10 //每页默认显示的数量

                            });
                            self.removeAttr('disabled');
                        } else {
                            layer.msg(res.msg);
                            $('#tracking_number').val('');
                            $('#inbound_order_number').val('');
                            $('#sea_cabinet_number').val('');

                            self.removeAttr('disabled');
                        }
                    },
                    error: function (e) {
                        layer.msg(e.message);
                        self.removeAttr('disabled');
                    }
                })
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
                        ids1.push(obj.data);
                    }else{
                        for(var i=0;i<checkStatus.data.length;i++){
                            ids1.push(checkStatus.data[i]);
                        }
                    }
                }else{
                    if(obj.type=='one'){
                        for(var i=0;i<ids.length;i++){
                            if(ids1[i] == checkStatus.data[i]){
                                console.log(ids[i].inbound_order_number );
                                ids1.splice(i,1);
                            }
                        }
                    }else{
                        for(var i=0;i<ids.length;i++){
                            for(var j=0;j<checkStatus.data.length;j++){
                                if(ids1[i]==checkStatus.data[j]){
                                    console.log(ids[i].inbound_order_number );
                                    ids1.splice(i,1);
                                }
                            }
                        }
                    }
                }
            });

            table.on('checkbox(demo1)', function (obj) {
                var checkStatus = table.checkStatus('demo1');
                if(checkStatus.isAll){
                    $(' .layui-table-header th[data-field="0"] input[type="checkbox"]').prop('checked', true);
                    $('.layui-table-header th[data-field="0"] input[type="checkbox"]').next().addClass('layui-form-checked');
                }

                if(obj.checked==true){
                    if(obj.type=='one'){
                        ids2.push(obj.data);
                    }else{
                        for (var i = 0; i < checkStatus.data.length; i++) {
                            ids2.push(checkStatus.data[i]);
                        }
                    }
                }else{
                    if(obj.type=='one'){
                        for(var i=0;i<ids2.length;i++){
                            if(ids2[i] == checkStatus.data[i]){
                                ids2.splice(i,1);
                            }
                        }
                    }else{
                        for(var i=0;i<ids2.length;i++){
                            for(var j=0;j<checkStatus.length;j++){
                                if(ids2[i]==checkStatus.data[j]){
                                    ids2.splice(i,1);
                                }
                            }
                        }
                    }
                }
            });

            // checkbox all
            form.on('checkbox(allChoose)', function (data) {
                $(this).parents('.layui-form').find("input[name='check[]']").each(function () {
                    this.checked = data.elem.checked;
                });
                form.render('checkbox');
            });
            form.on('checkbox(oneChoose)', function (data) {
                var i = 0;
                var j = 0;
                $(this).parents('.layui-form').find("input[name='check[]']").each(function () {
                    if( this.checked === true ){
                        i++;
                    }
                    j++;
                });
                if( i == j ){
                    $(this).parents('.layui-form').find(".checkboxAll").prop("checked",true);
                    form.render('checkbox');
                }else{
                    $(this).parents('.layui-form').find(".checkboxAll").removeAttr("checked");
                    form.render('checkbox');
                }

            });


        });
    </script>
@endsection
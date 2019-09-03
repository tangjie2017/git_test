@extends('layouts/app')

@section('content')
<div class="bgfff">
    <div class="container_full padd">
        <form class="layui-form" action="" id="myform">
            <input id="download_status" type="hidden" name="status" value="">
            <div class="tab1 widthinput">
                <div class="col">
                    <h5>{{ __('auth.name') }}</h5>
                    <input type="text" name="download_name"/>
                </div>
                <div class="col">
                    <h5>{{ __('auth.menu') }}</h5>
                    <select name="menu_id" >
                        <option value="">{{__('auth.pleaseSelect')}}</option>
                        @foreach(App\Models\Download::menuList() as $key => $menu)
                            <option value="{{$key}}">{{$menu}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <select name="time_type" >
                        <option value="1">{{ __('auth.CreationTime') }}</option>
                        <option value="2">{{ __('auth.UpdateTime') }}</option>
                    </select>
                </div>
                <div class="col">
                    <input type="text" name="time_during" class="layui-input" id="test10" placeholder="">
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
                            @foreach($downloadStatus as $k=>$v)
                                <em data-id="{{$k}}">{{ $v }}</em>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </form>

        <div class="resulttab">
            <table class="layui-hide" id="demo" lay-filter="demo"></table>
        </div>
    </div>
</div>
@endsection

@section('javascripts')
    <script type="text/html" id="barDemo">
        @{{#  if(d.status === 1 || d.status === 3){ }}
        <a href="javascript:void(0)" class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">{{ __('auth.delete') }}</a>
        @{{#  } }}

        @{{#  if(d.status === 2){ }}
        <a href="javascript:void(0)" class="layui-btn layui-btn-xs layui-btn-normal" lay-event="export">{{ __('auth.Export') }}</a>
        <a href="javascript:void(0)" class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">{{ __('auth.delete') }}</a>
        @{{#  } }}
    </script>
    <script>
        layui.use(['layer','form', 'laydate','table','element'], function(){
            var layer = layui.layer, form = layui.form, laydate = layui.laydate, table = layui.table, element = layui.element;
            var $ = layui.jquery;

            //日期时间范围
            laydate.render({
                elem: '#test10'
                ,type: 'datetime'
                ,range: true
            });

            //查询
            $("#submitForm").click(function () {
                form.on('submit(searBtn)',function(data){
                    table.render({
                        elem:'#demo'
                        ,url:'/task_center/download_task/download_list'
                        ,where:{data:data.field}
                        ,cols: [[ //标题栏
                            {title: 'NO',type:'numbers'}
                            ,{field: 'download_name', title: '{{ __('auth.name') }}', templet:function(d){
                                    return $.escapeHTML(d.download_name);
                                }}
                            ,{field:'menu_id', title: '{{ __('auth.menu') }}',templet:function(d){

                                    switch(d.menu_id){
                                        case 1:
                                            return '{{ __('auth.ReservationManagement') }}';
                                            break;
                                        default:
                                            return '{{ __('auth.ReturnCabinetManagement') }}';
                                    }
                                }}
                            ,{field:'status', title: '{{ __('auth.status') }}',templet:function(d){
                                    switch(d.status){
                                        case 1:
                                            return '{{ __('auth.processing') }}';
                                            break;
                                        case 2:
                                            return '{{ __('auth.processed') }}';
                                            break;
                                        default:
                                            return '{{ __('auth.fail') }}';
                                    }
                                }}
                            ,{field: 'xt', title: '{{ __('auth.time') }}', templet: function(d){
                                    var create_at,updated_at = '';
                                    if(d.created_at != null) {
                                        create_at = d.created_at.FormatToDate("yyyy-MM-dd hh:mm:ss");
                                    }

                                    if(d.updated_at != null) {
                                        updated_at = d.updated_at.FormatToDate("yyyy-MM-dd hh:mm:ss");
                                    }

                                    return '{{ __('auth.CreationTime') }}' + ':' + create_at + ' <br/>'+'{{ __('auth.UpdateTime') }}' + ':' + updated_at;
                                }}
                            ,{field: 'right', title: '{{ __('auth.operation') }}',toolbar: '#barDemo'}

                        ]]

                        ,even: true
                        ,page :true
                        ,limit: 10 //每页默认显示的数量
                        ,done:function () {   //返回数据执行回调函数
                            layer.close(index);    //返回数据关闭loading

                        }
                    });

                    return false;
                });
            })

            //数据列表
            table.render({
                elem: '#demo'
                ,url:'/task_center/download_task/download_list'
                ,cols: [[ //标题栏
                    {title: 'NO',type:'numbers'}
                    ,{field: 'download_name', title: '{{ __('auth.name') }}', templet:function(d){
                            return $.escapeHTML(d.download_name);
                     }}
                    ,{field:'menu_id', title: '{{ __('auth.menu') }}',templet:function(d){

                            switch(d.menu_id){
                                case 1:
                                    return '{{ __('auth.ReservationManagement') }}';
                                    break;
                                default:
                                    return '{{ __('auth.ReturnCabinetManagement') }}';
                            }
                        }}
                    ,{field:'status', title: '{{ __('auth.status') }}',templet:function(d){
                            switch(d.status){
                                case 1:
                                    return '{{ __('auth.processing') }}';
                                    break;
                                case 2:
                                    return '{{ __('auth.processed') }}';
                                    break;
                                default:
                                    return '{{ __('auth.fail') }}';
                            }
                        }}
                    ,{field: 'xt', title: '{{ __('auth.time') }}', templet: function(d){
                        var create_at,updated_at = '';
                        if(d.created_at != null) {
                            create_at = d.created_at.FormatToDate("yyyy-MM-dd hh:mm:ss");
                        }

                        if(d.updated_at != null) {
                            updated_at = d.updated_at.FormatToDate("yyyy-MM-dd hh:mm:ss");
                        }

                        return '{{ __('auth.CreationTime') }}' + ':' + create_at + ' <br/>'+'{{ __('auth.UpdateTime') }}' + ':' + updated_at;
                    }}
                    ,{field: 'right', title: '{{ __('auth.operation') }}',toolbar: '#barDemo'}

                ]]
                ,even: true
                ,page: true //是否显示分页
                ,limit: 10 //每页默认显示的数量
            });

            //点击按钮查询
            $('.multLable em').click(function(){
                var ro = $(this).attr('data-id');
                $("#download_status").val(ro);
                $("#submitForm").click();
            })

            table.on('tool(demo)', function(obj){
                var download_id = obj.data.download_id;
                var elect = $(this).parents('tr');
                //删除
                if(obj.event === 'del'){
                    layer.confirm($.getMessage('confirmDelete'),{
                        btn : [ $.getMessage('confirm'), $.getMessage('cancel') ],
                        yes: function(index){
                            $.MXAjax({
                                type: "post",
                                url: "/task_center/download_task/download_del",
                                data: {
                                    '_token':"<?php echo (csrf_token()); ?>" ,
                                    'download_id':download_id
                                },
                                success: function (data) {
                                    //获取这个ID并移除
                                    if (!data.Status){
                                        layer.msg(data.Message) ;
                                    }else{
                                        layer.msg($.getMessage('deleteSuccessFul')) ;
                                        elect.remove();
                                    }
                                }
                            });
                            layer.close(index);
                        }
                    })
                }
                //导出
                if(obj.event === 'export'){
                    window.location.href = '/task_center/download_task/download_export/'+download_id;
                }
            });

        });

    </script>
@endsection
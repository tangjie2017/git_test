@extends('layouts/app')

@section('css')
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/dtree.css') }}">
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/font/dtreefont.css') }}">
@endsection

@section('content')
<div class="bgfff">
    <div class="container_full padd butbot bott">
        <div class="layui-tab">
            <button type="button" class="layui-btn layui-btn-normal" id="xinzeng">{{ __('auth.NewNode') }}</button>
            <ul id="DemoTree" class="dtree" data-id="0"></ul>
        </div>
    </div>
    <div class="lookyuyuetext ftd" id="xinzengtext">
        <form action="" class="layui-form" id="myform">
            <table class="layui-table layui-form" lay-skin="nob">
                <tbody>
                <tr>
                    <td>{{ __("auth.name") }}</td>
                    <td><input type="text" name="route_name" value="" lay-verify="required" maxlength="50"/></td>
                </tr>
                <tr>
                    <td>{{ __("auth.EnglishName") }}</td>
                    <td><input type="text" name="en_name" value="" maxlength="50"/></td>
                </tr>
                <tr>
                    <td>URL</td>
                    <td><input type="text" name="url" value="" /></td>
                </tr>
                <tr>
                    <td>{{ __("auth.SuperiorNode") }}</td>
                    <td>
                        <select name="parent_route_id" lay-verify="required">
                            <option value="0">{{ __('auth.TopNode') }}</option>
                            @foreach($node as $item)
                                <option value="{{ $item['id'] }}">{{ $item['title'] }}</option>
                                @if(isset($item['children']))
                                    @foreach($item['children'] as $row)
                                        <option value="{{ $row['id'] }}">&nbsp;&nbsp;&nbsp;└ {{ $row['title'] }}</option>
                                        @if(isset($row['children']))
                                            @foreach($row['children'] as $v)
                                                <option value="{{ $v['id'] }}">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  └ {{ $v['title'] }}</option>
                                            @endforeach
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{{ __('auth.sort') }}</td>
                    <td><input type="text" name="sort" value="" /></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
@endsection

@section('javascripts')
<script>
    layui.use(['form', 'laydate','table','element','laypage','dtree'], function(){
        var layer = layui.layer,form = layui.form,laypage = layui.laypage,dtree = layui.dtree,laydate = layui.laydate;
        var element = layui.element;
        var $ = layui.jquery;

        //新增节点
        $(document).on('click','#xinzeng',function(){
            layer.open({
                type: 1,
                title: '{{ __('auth.add') }}',
                area: ['450px', '500px'],
                content: $('#xinzengtext'),
                btn: ['{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}'],
                yes: function(){
                    $.MXAjax({
                        type: "get",
                        url: "/menu_management/store_menu",
                        dataType:"json",
                        data:$("#myform").serialize(),
                        success: function (response) {
                            //获取这个ID并移除
                            if (!response.Status){
                                layer.msg(response.Message) ;
                            }else{
                                layer.msg(response.Message) ;
                                setTimeout(function () {
                                    window.location.reload();
                                }, 2000);
                            }
                        }
                    });
                    return false;
                }
            });
        });

        //树形结构
        var DemoTree = dtree.render({
            elem: "#DemoTree",
            data: <?php echo $tree ?>,
            checkbar: true,
            checkbarType: "p-casc",
            menubar:true,
            checkbar: true,
            dot: false,
            // icon:"-1",
            //删除节点
            menubarFun : {
                remove: function(checkbarNodes){
                    var ids = new Array();
                    $.each(checkbarNodes, function(idx, obj) {
                        ids.push(obj.nodeId);

                    });
                    var strIds = ids.join(',')
                    $.MXAjax({
                        url:'/menu_management/del_menu',
                        type:'get',
                        data:{ids:strIds},
                        dataTyper:'json',
                        success:function(data){
                            if(data.Status !== 1){
                                layer.msg(data.Message,{icon:5});
                                return false;
                            }

                        },
                    });
                    layer.msg('{{ __("auth.deleteSuccess") }}',{icon:6});
                    return true;

                }
            }
        });

        //编辑节点
        dtree.on("node('DemoTree')",function(obj){
            var route_id = obj.param.nodeId
            layer.open({
                type: 2,
                shadeClose:true,
                title: '{{ __("auth.edit") }}',
                area: ['450px', '500px'],
                content: '{{ url('menu_management/edit') }}'+'/'+route_id,
                btn: ['{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}'],
                btn1: function(index, layero){
                    var info = window["layui-layer-iframe" + index].callbackdata();
                    $.MXAjax({
                        url:'/menu_management/store_menu',
                        type:'get',
                        data: {
                            'route_id': info.route_id ,
                            'route_name': info.route_name ,
                            'en_name' : info.en_name ,
                            'url' : info.url,
                            'sort' : info.sort
                        },
                        dataType:'json',
                        success:function(data){
                            if(data.Status == 1){
                                layer.msg(data.Message,{icon:6});
                                layer.close(index);
                            }else {
                                layer.msg(data.Message,{icon:5});
                                layer.close(index);
                            }
                        },
                        error: function(msg){
                            var json = JSON.parse(msg.responseText);
                            $.each(json, function(idx, obj) {
                                layer.msg(obj[0],{icon:5});
                                return false;
                            });
                        }
                    });

                }
            });
        });

    });
</script>
@endsection
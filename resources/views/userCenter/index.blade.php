@extends('layouts/app')

@section('content')
<div class="bgfff">
    <div class="container_full padd">
        <form class="layui-form" action="">
            <input type="hidden" name="status" id="user_status" value=""/>
            <div class="tab1 widthinput">
                <div class="col">
                    <h5>{{ __('auth.role') }}</h5>
                    <select name="role_id" >
                        <option value=""></option>
                        @foreach($role as  $value)
                            <option value="{{ $value['role_id'] }}">{{ $value['role_name'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col">
                    <h5>{{ __('auth.UserAccount') }}</h5>
                    <input type="text" name="user_code" />
                </div>
                <div class="col">
                    <h5>{{__('auth.Username')}}</h5>
                    <input type="text" name="user_name" />
                </div>
                <div class="col">
                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="searBtn" id="submitForm">{{ __('auth.search') }}</button>
                </div>
                <div class="clear"></div>
            </div>
            <div class="tab1 timechoos marginwidth nomargin">
                <div class="col">
                    <h5>{{ __('auth.status') }}</h5>
                    <div class="inputBlock">
                        <div class="multLable">
                            <em class="curr">{{ __('auth.all') }}</em>
                            <em data-id="1">{{ __('auth.enable') }}</em>
                            <em data-id="2">{{ __('auth.disabled') }}</em>
                        </div>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </form>
        <div class="resulttab">
            <table class="layui-hide" id="demo" lay-filter="demo"></table>
        </div>
        <div class="lookyuyuetext" id="userquanxt">
            <ul id="DemoTree" class="dtree" data-id="0"></ul>
        </div>

    </div>
</div>

<script type="text/html" id="barDemo">
    <a class="layui-btn layui-btn-xs" lay-event="look">{{ __('auth.view') }}</a>
    <a class="layui-btn layui-btn-xs" lay-event="edit">{{ __('auth.edit') }}</a>
    <a class="layui-btn layui-btn-xs" lay-event="auth">{{ __('auth.permission') }}</a>
</script>
@endsection

@section('javascripts')
<script>
    layui.use(['form', 'laydate','table','element','laypage','dtree'], function(){
        var layer = layui.layer,
            form = layui.form,
            laypage = layui.laypage,
            laydate = layui.laydate;
        var element = layui.element;
        var $ = layui.jquery;
        var table = layui.table;

        var currPage = '';
        //监听行工具事件
        table.on('tool(demo)', function(obj){
            var data = obj.data;
            //console.log(obj)
            if(obj.event === 'auth'){
                var role_id = data.role_id;
                layer.open({
                    type: 2,
                    title: '{{ __('auth.permission') }}',
                    area: ['600px', '750px'],
                    shadeClose: true,//点击外围关闭弹窗
                    scrollbar: false,
                    content:"{{url('/user_center/auth')}}"+"?id="+data.user_id+"&role_id="+role_id,
                    btn: ['{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}'],
                    yes:function (index) {
                        var info = window["layui-layer-iframe" + index].callbackdata();
                        if(info.length == 0) {
                            alert('{{ __("auth.PleaseAssignPermissionsToTheRoleFirst") }}');
                            layer.close(index);
                            return false;
                        }
                        var user_id =  data.user_id;
                        $.MXAjax({
                            type:'post',
                            data:{
                                info:info,
                                user_id:user_id,
                                _token:"<?php echo (csrf_token()); ?>"
                            },
                            url:'/user_center/assignAccess',
                            success:function (response) {
                                layer.msg(response.Message);
                                if(response.Status){
                                    setTimeout(function(){  //使用  setTimeout（）方法设定定时1000毫秒
                                        layer.close(index);
                                        showData();
                                        //window.parent.location.reload();//刷新父页面
                                    },1000);
                                }
                            }
                        });
                    }
                });
            } else if(obj.event === 'edit'){
                layer.open({
                    type: 2,
                    title: '{{ __("auth.edit") }}',
                    shadeClose: true,//点击外围关闭弹窗
                    area: ['850px', '700px'],
                    content: ["{{url('/user_center/edit')}}"+"?id="+data.user_id,'no'],
                    btn: ['{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}'],
                    yes: function(index){
                        var info = window["layui-layer-iframe" + index].callbackdata();
                        var warehouse = info.chk_value;
                        var content = info.content;
                        var role_id = info.role_id;
//                        console.log(role_id);return false;
                        $.ajax({
                            type: 'post',
                            data:{
                                warehouse:warehouse,
                                role_id : role_id,
                                _token:"<?php echo (csrf_token()); ?>"
                            },
                            dataType:'json',
                            url: '/user_center/warehouse'+"?id="+data.user_id,
                            success: function(response){
                                    layer.msg(response.Message);
                                if(response.Status){
                                    setTimeout(function(){  //使用  setTimeout（）方法设定定时1000毫秒
                                        layer.close(index);
                                        showData();
                                        //window.parent.location.reload();//刷新父页面
                                    },1000);
                                }
                            }
                        })
                }
            })
            }else if(obj.event === 'look'){
                layer.open({
                    type: 2,
                    title: '{{ __("auth.view") }}',
                    shadeClose: true,//点击外围关闭弹窗
                    area: ['850px', '600px'],
                    content: ["{{url('/user_center/userlook')}}"+"?user_id="+data.user_id,'no'],
                    btn: ['{{ __("auth.Cancel") }}'],
                    yes:function (index) {
                        layer.close(index);
                    }
                });
            }
        });

        function showData() {
            t.reload({
                page:{curr : currPage}
            });
        }

        form.on('submit(searBtn)',function (data) {
            //展示数据
            t.reload({
                where: {data: data.field},
                page:{curr : 1}
            });

            return false;
        });

        //展示数据
        var t = table.render({
            elem: '#demo'
            , url: '/user_center/search'
            , cols: [[{title: 'NO',type:'numbers'}
                ,{field: 'user_code', title: '{{ __("auth.UserAccount") }}'}
                ,{field: 'user_name', title: '{{ __("auth.Username") }}'}
                ,{field: 'en_name', title: '{{ __("auth.EnglishName") }}'}
                ,{field: 'role_name', title: '{{ __("auth.role") }}',templet:function(d){
                    if(d.role_id == null){
                        return '';
                    }else{
                        if(d.role== null){
                            return '';
                        }
                        return d.role.role_name;
                    }
                }}
                ,{field: 'userWarehouse', title: '{{ __("auth.BindingWarehouse") }}'}
                ,{field: 'status', title: '{{ __("auth.status") }}',templet:function (d) {
                    switch (d.status){
                        case 1:
                            return '{{ __("auth.enable") }}';
                            break;
                        default:
                            return '{{ __("auth.disabled") }}';
                    }
                }}
                ,{field: 'cz', title: '{{ __("auth.operation") }}',toolbar: '#barDemo'}
            ]]
            ,done:function (data,curr){
                currPage = curr;
            }
            ,even: true
            ,page :true
            ,limit: 10 //每页默认显示的数量
//            ,totalRow: true
        });

        //点击按钮查询
        $('.multLable em').click(function(){
            var ro = $(this).attr('data-id');
            $("#user_status").val(ro);
            $("#submitForm").click();
        });

    });

    //下拉框交换JQuery
    $(function(){
        $('#add').click(function() {
            $('#select1 option:selected').appendTo('#select2');
        });
        $('#remove').click(function() {
            $('#select2 option:selected').appendTo('#select1');
        });
        $('#add_all').click(function() {
            $('#select1 option').appendTo('#select2');
        });
        $('#remove_all').click(function() {
            $('#select2 option').appendTo('#select1');
        });
        $('#select1').dblclick(function(){
            $("option:selected",this).appendTo('#select2');
        });
        $('#select2').dblclick(function(){
            $("option:selected",this).appendTo('#select1');
        });
    });
    //下拉框交换JQuery2222222222
    $(function(){
        $('#add1').click(function() {
            $('#select1-1 option:selected').appendTo('#select2-2');
        });
        $('#remove1').click(function() {
            $('#select2-2 option:selected').appendTo('#select1-1');
        });
        $('#add_all1').click(function() {
            $('#select1-1 option').appendTo('#select2-2');
        });
        $('#remove_all1').click(function() {
            $('#select2-2 option').appendTo('#select1-1');
        });
        $('#select1-1').dblclick(function(){
            $("option:selected",this).appendTo('#select2-2');
        });
        $('#select2-2').dblclick(function(){
            $("option:selected",this).appendTo('#select1-1');
        });
    });
    //下拉框交换JQuery33333333333333
    $(function(){
        $('#add2').click(function() {
            $('#select1-3 option:selected').appendTo('#select2-3');
        });
        $('#remove2').click(function() {
            $('#select2-3 option:selected').appendTo('#select1-3');
        });
        $('#add_all2').click(function() {
            $('#select1-3 option').appendTo('#select2-3');
        });
        $('#remove_all2').click(function() {
            $('#select2-3 option').appendTo('#select1-3');
        });
        $('#select1-3').dblclick(function(){
            $("option:selected",this).appendTo('#select2-3');
        });
        $('#select2-3').dblclick(function(){
            $("option:selected",this).appendTo('#select1-3');
        });
    });

</script>
@endsection

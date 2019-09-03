@extends('layouts/app')

@section('css')
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/dtree.css') }}">
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/font/dtreefont.css') }}">
@endsection

@section('content')
<div class="bgfff">
    <div class="container_full padd">
        <form class="layui-form" action="">
            <input type="hidden" name="status" id="user_status" value=""/>
            <div class="tab1 widthinput">
                <div class="col">
                    <h5>{{ __('auth.RoleName') }}</h5>
                    <input type="text" name="role_name"/>
                </div>

                <div class="col">
                    <h5>{{ __('auth.EnglishName') }}</h5>
                    <input type="text" name="en_name"/>
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
                <div class="colr"><button type="button" class="layui-btn layui-btn-normal xinzeng">{{ __("auth.add") }}</button></div>
                <div class="clear"></div>
            </div>

            <div class="resulttab">
                <table class="layui-hide" id="demo" lay-filter="demo"></table>
            </div>

        </form>
    </div>
</div>

<!-- 新增弹窗 -->
<div class="lookyuyuetext" id="xinzeng">
    <div class="layui-form">
        <table class="layui-table" lay-skin="nob">
            <tbody>
            <tr>
                <td>{{ __('auth.RoleName') }}</td>
                <td><input type="text" value=""  id="role_name"/></td>
            </tr>
            <tr>
                <td>{{ __('auth.EnglishName') }}</td>
                <td><input type="text" value=""  id="en_name"/></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

{{--编辑弹窗--}}
<div class="lookyuyuetext" id="bianjitext">
    <input type="hidden" name="role_id" id="role_id" />
    <div class="layui-form">
        <table class="layui-table" lay-skin="nob">
            <tbody>
            <tr>
                <td>{{ __('auth.RoleName') }}</td>
                <td><input type="text" value="" id="rolename"/></td>
            </tr>
            <tr>
                <td>{{ __('auth.EnglishName') }}</td>
                <td><input type="text" value="" id="enname"/></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<script type="text/html" id="barDemo">
    @{{# if( d.status == 1 )  { }}
    <a class="layui-btn layui-btn-xs  layui-btn-normal" lay-event="auth">{{ __('auth.permission') }}</a>
    <a class="layui-btn layui-btn-xs" lay-event="edit">{{ __('auth.edit') }}</a>
    <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="stop">{{ __('auth.disabled') }}</a>
    @{{# } }}

    @{{# if( d.status == 2 )  { }}
    <a class="layui-btn layui-btn-xs  layui-btn-normal" lay-event="start">{{ __('auth.enable') }}</a>
    <a class="layui-btn layui-btn-xs" lay-event="edit">{{ __('auth.edit') }}</a>
    <a class="layui-btn layui-btn-xs layui-btn-danger" lay-event="del">{{ __('auth.delete') }}</a>
    @{{# } }}
</script>
@endsection

@section('javascripts')
<script>
    layui.use(['form', 'laydate','table','element','laypage','dtree'], function(){
        var layer = layui.layer,form = layui.form,laypage = layui.laypage,table = layui.table,dtree = layui.dtree,laydate = layui.laydate;
        var element = layui.element;
        var $ = layui.jquery;

        var currPage = '';
        //表格监听
        table.on('tool(demo)', function(obj) {
            var data = obj.data;
            //console.log(obj)
            if (obj.event === 'auth') {
                layer.open({
                    type: 2,
                    title: '{{ __('auth.permission') }}',
                    area: ['500px', '700px'],
                    shadeClose: true,//点击外围关闭弹窗
                    scrollbar: false,
                    content:"{{url('/role/giveAccess')}}"+"?id="+data.role_id,
                    btn: ['{{ __('auth.Determine') }}', '{{ __('auth.Cancel') }}'],
                    yes:function (index) {
                        var info = window["layui-layer-iframe" + index].callbackdata();
                        var role_id =  data.role_id;
                        $.MXAjax({
                           type:'post',
                            data:{
                                info:info,
                                role_id:role_id,
                                _token:"<?php echo (csrf_token()); ?>"
                            },
                            url:'/role/assignAccess',
                            success:function (response) {
                                layer.msg(response.Message);
                                if(response.Status){
                                    layer.close(index);
                                    setTimeout(function(){  //使用  setTimeout（）方法设定定时1000毫秒
                                        showData();
                                        //window.parent.location.reload();//刷新父页面
                                    },1000);
                                }
                            }
                        });
                    }
                });
            }else if(obj.event === 'edit') {
                var role_id =  data.role_id;
                var role_name = data.role_name;
                var en_name = data.en_name;
                $("#rolename").val(role_name);
                $("#enname").val(en_name);
                layer.open({
                    type: 1,
                    title: '{{ __('auth.edit') }}',
                    area: ['400px', '250px'],
                    shadeClose: true,//点击外围关闭弹窗
                    scrollbar: false,
                    content: $('#bianjitext'),
                    btn: ['{{ __('auth.Determine') }}', '{{ __('auth.Cancel') }}'],
                    yes:function (index) {
                        var role_name = $("#rolename").val();
                        var en_name = $("#enname").val();
                        $.MXAjax({
                            type: 'post',
                            data:{
                                role_id : role_id,
                                role_name:role_name,
                                en_name:en_name,
                                _token:"<?php echo (csrf_token()); ?>"
                            },
                            dataType:'json',
                            url: '/role/addAndUpdate',
                            success: function(response){
                                layer.msg(response.Message);
                                if(response.Status){
                                    layer.close(index);
                                    setTimeout(function(){  //使用  setTimeout（）方法设定定时1000毫秒
                                        showData();
                                        //window.parent.location.reload();//刷新父页面
                                    },1000);
                                }
                            }
                        })
                    }
                });
            }else if(obj.event === 'stop') {
                var role_id =  data.role_id;
//                console.log(role_id);return false;
                layer.confirm('{{ __("auth.ConfirmDeactivateTheCurrentRole") }}',{
                btn : [ '{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}' ],
                yes: function(index){
                    $.MXAjax({
                            type:'post',
                            data:{
                                'role_id':role_id,
                                _token:"<?php echo (csrf_token()); ?>"
                            },
                            dataType:'json',
                            url:'/role/stop',
                            success:function (response) {
                                layer.msg(response.Message);
                                layer.close(index);
                                if(response.Status){
                                    setTimeout(function(){  //使用  setTimeout（）方法设定定时1000毫秒
                                        showData();
                                        //window.parent.location.reload();//刷新父页面
                                    },1000);
                                }
                            }
                        });
                    }
            })
            }else if(obj.event === 'del'){
                var role_id =  data.role_id;
                layer.confirm('{{ __("auth.ConfirmDeleteTheCurrentRole") }}',{
                    btn : [ '{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}' ],
                    yes: function(index){
                        $.MXAjax({
                            type:'post',
                            data:{
                                'role_id':role_id,
                                _token:"<?php echo (csrf_token()); ?>"
                            },
                            dataType:'json',
                            url:'/role/delete',
                            success:function (response) {
                                layer.msg(response.Message);
                                layer.close(index);
                                if(response.Status){
                                    //obj.del();
                                    setTimeout(function(){  //使用  setTimeout（）方法设定定时1000毫秒
                                        showData();
                                        //window.parent.location.reload();//刷新父页面
                                    },1000);
                                }
                            }
                        });
                    }
                })
            }else if(obj.event === 'start'){
                var role_id =  data.role_id;
                layer.confirm('{{ __("auth.ConfirmTheCurrentRoleEnabled") }}',{
                    btn : [ '{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}' ],
                    yes: function(index){
                        $.MXAjax({
                            type:'post',
                            data:{
                                'role_id':role_id,
                                _token:"<?php echo (csrf_token()); ?>"
                            },
                            dataType:'json',
                            url:'/role/start',
                            success:function (response) {
                                layer.msg(response.Message);
                                layer.close(index);
                                if(response.Status){
                                    //obj.del();
                                    setTimeout(function(){  //使用  setTimeout（）方法设定定时1000毫秒
                                        showData();
                                        //window.parent.location.reload();//刷新父页面
                                    },1000);
                                }
                            }
                        });
                    }
                })
            }
        });


       //新增弹窗效果
        $(document).on('click','.xinzeng',function(){
            layer.open({
                type: 1,
                title: '{{ __("auth.add") }}',
                area: ['400px', '250px'],
                content: $('#xinzeng'),
                btn: ['{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}'],
                yes: function(index){
                    var role_name = $("#role_name").val();
                    var en_name = $("#en_name").val();
                    $.MXAjax({
                        type: 'post',
                        data:{
                            role_name:role_name,
                            en_name:en_name,
                            _token:"<?php echo (csrf_token()); ?>"
                        },
                        dataType:'json',
                        url: '/role/addAndUpdate',
                        success: function(response){
                            layer.msg(response.Message);
                            if(response.Status){
                                layer.close(index);
                                setTimeout(function(){  //使用  setTimeout（）方法设定定时1000毫秒
                                    showData();
                                    //window.parent.location.reload();//刷新父页面
                                },1000);
                            }
                        }
                    })

                },
                end:function () {
                    $("#role_name").val('');
                    $("#en_name").val('');
                }
            });
        });

        //点击按钮查询
        $('.multLable em').click(function(){
            var ro = $(this).attr('data-id');
            $("#user_status").val(ro);
            $("#submitForm").click();
        });

        form.on('submit(searBtn)',function (data) {
            //展示数据
            t.reload({
                where: {data: data.field},
                page:{curr : 1}
            });

            // $('#myform')[0].reset();
            return false;
        });

        //展示数据
       var t = table.render({
            elem: '#demo'
            , url: '/role/search'
            , cols: [[{title: 'NO',type:'numbers'}
                ,{field: 'role_name', title: "{{ __('auth.RoleName') }}"}
                ,{field: 'status', title: '{{ __('auth.status') }}',templet:function (d) {
                    switch (d.status){
                        case 1:
                            return "{{ __('auth.enable') }}";
                            break;
                        default:
                            return "{{ __('auth.disabled') }}";
                    }
                }}
                ,{field: 'en_name', title: '{{ __('auth.EnglishName') }}'}
                ,{field: 'cz', title: '{{ __('auth.operation') }}',toolbar: '#barDemo'}
            ]]
           ,done:function (data,curr){
               currPage = curr;
           }
            ,even: true
            ,page :true
            ,limit: 10 //每页默认显示的数量
//            ,totalRow: true
        });


        function showData() {
            t.reload({
                page:{curr : currPage}
            });
        }

    });
</script>
@endsection
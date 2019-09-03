@extends('layouts/app')

@section('css')
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/dtree.css') }}">
    <link rel="stylesheet" href="{{ asset('layui_ext/dtree/font/dtreefont.css') }}">
@endsection

@section('content')
    <style>
        body {
            min-width: 450px!important;
        }

        .container_full{
            margin-top: 10px;
        }
    </style>
<div class="bgfff">
    <div class="container_full padd butbot bott">
        <div class="layui-tab">
            <ul id="DemoTree" class="dtree" data-id="0"></ul>
        </div>
    </div>
</div>
@endsection

@section('javascripts')
<script>
    layui.use(['form', 'laydate','table','element','laypage','dtree'], function(){
        var layer = layui.layer,form = layui.form,laypage = layui.laypage,dtree = layui.dtree,laydate = layui.laydate;
        var element = layui.element;
        var $ = layui.jquery;

        window.callbackdata = function () {
            return dtree.getCheckbarNodesParam("DemoTree");
        };


        //树形结构
        var DemoTree = dtree.render({
            elem: "#DemoTree",
            data: <?php echo $tree ?>,
            checkbar: true,
            checkbarType: "all",
            checkbar: true,
            dot: false,

        });


    });
</script>
@endsection

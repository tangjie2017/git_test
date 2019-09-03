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
    </style>
<div class="bgfff" style="width: 450px; height: 380px;">
    <div class="lookyuyuetext ftd" id="xinzengtext" style="display: block">
        <form action="" class="layui-form" id="myform">
            <input type="hidden" class="route_id" name="route_id" value="{{ $route['route_id'] }}">
            <table class="layui-table layui-form" lay-skin="nob">
                <tbody>
                <tr>
                    <td>{{ __("auth.name") }}</td>
                    <td><input type="text" class="route_name" name="route_name" value="{{ $route['route_name'] }}" lay-verify="required" maxlength="50"/></td>
                </tr>
                <tr>
                    <td>{{ __("auth.EnglishName") }}</td>
                    <td><input type="text" class="en_name" name="en_name" value="{{ $route['en_name'] }}" maxlength="50"/></td>
                </tr>
                <tr>
                    <td>URL</td>
                    <td><input type="text" class="url" name="url" value="{{ $route['url'] }}" /></td>
                </tr>
                <tr>
                    <td>{{ __("auth.SuperiorNode") }}</td>
                    <td>
                        <select name="parent_route_id" lay-verify="required" disabled>
                            <option value="0" {{ $route['parent_route_id'] == 0 ? 'selected':'' }}>{{ __('auth.TopNode') }}</option>
                            @foreach($node as $item)
                                <option value="{{ $item['id'] }}" {{ $route['parent_route_id'] == $item['id'] ? 'selected':'' }}>{{ $item['title'] }}</option>
                                @if(isset($item['children']))
                                    @foreach($item['children'] as $row)
                                        <option value="{{ $row['id'] }}" {{ $route['parent_route_id'] == $row['id'] ? 'selected':'' }}>&nbsp;&nbsp;&nbsp;└ {{ $row['title'] }}</option>
                                        @if(isset($row['children']))
                                            @foreach($row['children'] as $v)
                                                <option value="{{ $v['id'] }}" {{ $route['parent_route_id'] == $v['id'] ? 'selected':'' }}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  └ {{ $v['title'] }}</option>
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
                    <td><input type="text" class="sort" name="sort" value="{{ $route['sort'] }}" /></td>
                </tr>
                </tbody>
            </table>
        </form>
    </div>
</div>
@endsection

@section('javascripts')
    <script>
        var callbackdata = function () {
            var data = {
                route_id: $('.route_id').val(),
                route_name: $('.route_name').val(),
                en_name: $('.en_name').val(),
                url: $('.url').val(),
                sort: $('.sort').val(),
            };
            return data;
        };
        layui.use(['layer','form','element','upload'], function(){
            var layer = layui.layer,form = layui.form,upload = layui.upload,element = layui.element;



        });
    </script>
@endsection
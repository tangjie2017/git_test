@extends('layouts/dialog')

@section('content')
<div class="lookyuyuetext" id="emiltext" style="display:block;width:450px; height:125px; margin-top:40px;padding: 10px 20px;position:relative">
    <table class="layui-table layui-form firrig" lay-skin="nob" >
        <tbody>
        <tr>
            <td>{{ __('auth.NotifyReturnTime') }}：</td>
            <td><input type="text" class="layui-input" id="notice_return_time" value="{{ \App\Models\Warehouse::switchTimeZone($res['notice_return_time']) }}" placeholder="{{ __('auth.EntryCorrespondingTimeLogin') }}" style="width: 200px"/></td>
        </tr>
        <tr>
            <td>{{ __('auth.email') }}：</td>
            <td><input type="text" class="email" id="email" value="{{ $res['rem']['email'] }}"  style="width: 200px"/></td>
        </tr>
        </tbody>
    </table>
</div>
@endsection

@section('javascripts')
    <script>
        var callbackdata = function () {
            var data = {
                email: $('.email').val(),
                notice_return_time: $('#notice_return_time').val(),

            };
            return data;
        };

        layui.use(['form', 'laydate','table','element','laypage'], function() {
            var laydate = layui.laydate;
            laydate.render({
                elem: '#notice_return_time'
                ,type: 'datetime'
            });
        })
     </script>

@endsection
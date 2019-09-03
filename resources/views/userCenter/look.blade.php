@extends('layouts/dialog')

@section('content')
    <style>
        body  {
            min-width: 900px; !important;
            padding: 10px 30px;
        }
    </style>
<!-- 查看弹窗 -->
<div  id="userlook" class="lookyuyuetext" style="display:block; padding: 10px 30px; width: 800px; height:750px ">
    <div class="layui-form lookyy">
        <table class="layui-table" lay-skin="nob">
            <h3>{{ __('auth.BasicInformation') }}</h3>
            <tbody>
            <tr>
                <td style="width: 350px">{{ __('auth.UserAccount') }} :{{ $data['user_code'] }}</td>
                <td style="width: 350px">{{__('auth.Username')}} ： {{  $data['user_name'] }}</td>
            </tr>
            <tr>
                <td style="width: 350px">{{ __('auth.EnglishName') }} : {{  $data['en_name'] }}</td>
                <td style="width: 350px">{{ __('auth.Password') }} : ******</td>
            </tr>
            {{--<tr>--}}
                {{--<td>邮箱</td>--}}
                {{--<td><input type="password" readonly/></td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<td>联系电话</td>--}}
                {{--<td><input type="text" readonly/></td>--}}
                {{--<td>状态</td>--}}
                {{--<td><input type="text" value="{{ $data['status'] ==1 ?'启用':'停用'}}" readonly/></td>--}}
            {{--</tr>--}}
            {{--<tr>--}}
                {{--<td>备注</td>--}}
                {{--<td colspan="3"><textarea name="" placeholder="请输入内容" class="layui-textarea"></textarea></td>--}}
            {{--</tr>--}}
            </tbody>
        </table>
    </div>
    <div class="lookyy">
        <table class="layui-table" lay-skin="nob">
            <h3>{{ __("auth.AccessingWarehouseBindings") }}</h3>
            <tbody>
            <tr>
                <td>{{ __("auth.warehouse") }}</td>
                <td></td>
                <td>{{ __("auth.bind") }}</td>
            </tr>
            <tr>
                <td width="200">
                    <div>
                        <select multiple="multiple" id="select1" class="changetext" readonly="">
                            @foreach($warehouse as $v)
                                <option value="{{ $v }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                </td>
                <td width="80">
                    <div class="exchange">
                        <i id="add" class="layui-icon layui-icon-right"></i><br />
                        <i id="add_all" class="layui-icon layui-icon-next"></i><br />
                        <i id="remove" class="layui-icon layui-icon-left"></i><br />
                        <i id="remove_all" class="layui-icon layui-icon-prev"></i>
                    </div>
                </td>
                <td>
                    <div>
                        <select multiple="multiple" id="select2"  class="changetext">
                            @if(isset($data['UserWarehouse']))
                                @foreach($data['UserWarehouse'] as $v)
                                    <option value="{{$v['warehouse_name']}}[{{$v['warehouse_code']}}]">{{$v['warehouse_name']}}[{{$v['warehouse_code']}}]</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
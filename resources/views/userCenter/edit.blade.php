@extends('layouts/dialog')

@section('content')
    <style>
        body  {
            min-width: 900px; !important;
            padding: 10px 30px;
        }
    </style>
    <!-- 查看弹窗 -->
    <div  id="edit" class="lookyuyuetext" style="display:block; padding: 10px 30px; width: 800px; height:700px ">
        <div class="layui-form lookyy">
            <h3>{{ __("auth.BasicSettings") }}</h3>
            <table class="layui-table" lay-skin="nob">
                <tbody>
                <tr>
                    <td style="width: 350px">{{ __('auth.UserAccount') }} :{{ $data['user_code'] }}</td>
                    <td style="width: 350px">{{__('auth.Username')}} ： {{  $data['user_name'] }}</td>
                </tr>
                <tr>
                    <td style="width: 350px">{{ __('auth.EnglishName') }} : {{  $data['en_name'] }}</td>
                    <td style="width: 350px">{{ __('auth.Password') }} : ******</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="lookyy">
                <h3>{{ __('auth.role') }}</h3>
                <table class="layui-table" lay-skin="nob">
                    <tbody>
                    <tr>
                        <td>{{ __('auth.role') }}：&nbsp;&nbsp;
                            <select name="role_id" id="role_id">
                                <option value="">{{$role_name}}</option>
                                @foreach($role as  $value)
                                    <option value="{{ $value['role_id'] }}">{{ $value['role_name'] }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
        </div>

        <div class="lookyy">
            <h3>{{ __("auth.AccessingWarehouseBindings") }}</h3>
            <table class="layui-table" lay-skin="nob">
                <tbody>
                <tr>
                    <td>{{ __("auth.warehouse") }}</td>
                    <td></td>
                    <td>{{ __("auth.bind") }}</td>
                </tr>
                <tr>
                    <td width="200">
                        <div>
                            <select multiple="multiple" id="select1" class="changetext">
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

@section('javascripts')
    <script>

        //页面数据
        var callbackdata = function () {
            var content = $('#content').val();
            var role_id = $('#role_id option:selected').val();
            var chk_value =[];//定义一个数组
            $('#select2 option').each(function(){//遍历每一个名字为interest的复选框，其中选中的执行函数
                chk_value.push($(this).val());//将选中的值添加到数组chk_value中
            });
            var data = {
                chk_value: chk_value,
                content: content,
                role_id:role_id,
            };
            return data;
        }

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
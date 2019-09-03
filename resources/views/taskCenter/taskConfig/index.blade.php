@extends('layouts/app')

@section('content')
    <div class="bgfff">
        <div class="container_full paddchuangjian">
            <div class="layui-tab marginauto">
                <ul class="layui-tab-title">
                    <li class="layui-this">{{__('auth.TaskConfiguration')}}</li>
                    <li>{{__('auth.DownloadConfiguration')}}</li>
                </ul>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <div class="chuangjian">
                            <form action="" class="layui-form myform">
                                <input type="hidden" name="message_notice_content" value="{{ isset($config['message_notice_content']) ? $config['message_notice_content'] : ''}}">
                                <input type="hidden" name="email_notice_content" value="{{ isset($config['email_notice_content']) ? $config['email_notice_content'] : ''}}">
                                <div class="establish lefth5">
                                    <h5>{{__('auth.turnOnReminders')}}</h5>
                                    <input type="checkbox" name="is_notice_open" @if(isset($config['is_notice_open']) && $config['is_notice_open'] == 1) checked @endif
                                    lay-skin="switch" value="1" lay-text="ON|OFF">
                                </div>
                                <div class="establish lefth5">
                                    <h5>{{__('auth.PromptConfiguration')}}</h5>
                                    <table  class="layui-table" lay-skin="nob">
                                        <tbody>
                                        <tr>
                                            <td>{{__('auth.theRemainingTime')}}</td>
                                            <td>
                                                <select disabled>
                                                    <option value="0"> < </option>

                                                </select>
                                            </td>
                                            <td><input type="text" name="remaining_time" lay-verify="number" maxlength="5" placeholder="{{__('auth.unit')}}" value="{{ isset($config['remaining_time']) ? $config['remaining_time'] : ''}}"/></td>
                                        </tr>
                                        <tr>
                                            <td>{{__('auth.overtimeTime')}}</td>
                                            <td>
                                                <select  disabled>
                                                    <option value="0"> > </option>
                                                </select>
                                            </td>
                                            <td><input type="text" name="over_time" maxlength="5"lay-verify="number"  placeholder="{{__('auth.unit')}}" value="{{ isset($config['over_time']) ? $config['over_time'] : ''}}"/></td>
                                        </tr>
                                        <tr>
                                            <td>{{__('auth.reminderInterval')}}</td>
                                            <td><input type="text" name="interval_time" maxlength="10" lay-verify="number" value="{{ isset($config['interval_time']) ? $config['interval_time'] : ''}}" placeholder="{{__('auth.unit')}}"/></td>
                                            <td></td>
                                            <td>{{__('auth.numberOfReminders')}}</td>
                                            <td><select name="frequency">
                                                    <option value="">{{__('auth.pleaseSelect')}}</option>
                                                    @foreach(\App\Services\TaskConfigService::getFrequency() as $k => $v)
                                                        <option value="{{ $k }}" @if(isset($config['frequency']) && $k == $config['frequency']) selected @endif>{{ $v }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="establish lefth5">
                                    <h5>{{__('auth.SMS')}}</h5><input type="checkbox" name="message_open"  @if(isset($config['message_open']) && $config['message_open'] == 1) checked @endif
                                    lay-skin="switch" value="1" lay-text="ON|OFF">
                                    <h5>{{__('auth.whetherToPromptSuppliers')}}</h5><input type="checkbox" name="message_notice_supplier" @if(isset($config['message_notice_supplier']) && $config['message_notice_supplier'] == 1) checked @endif
                                    value="1" lay-skin="switch" lay-text="ON|OFF">
                                    <a href="javascript:void(0)" class="layui-btn layui-btn-normal mesContent">{{__('auth.reservationContent')}}</a>
                                    <table  class="layui-table consigneeTable" lay-skin="nob">
                                        <tbody>
                                        @if(!empty($mesConsignee))
                                        @foreach($mesConsignee as $k => $v)
                                        <tr>
                                            <td>{{__('auth.recipientName')}}</td>
                                            <td><input type="text" name="messageName[]" maxlength="30" value="{{ isset($v['consignee_name']) ? $v['consignee_name'] : "" }}"/></td>
                                            <td>{{__('auth.recipientTel')}}</td>
                                            <td><input type="text" name="messageTel[]" maxlength="20" value="{{ isset($v['consignee_telephone']) ? $v['consignee_telephone'] : ''}}"/></td>
                                            @if($k == 0)
                                            <td><button  type="button" class="layui-btn  layui-btn-xs layui-btn-normal KBbinning"><i class="layui-icon layui-icon-add-1"></i></button></td>
                                            @else
                                            <td><button type="button" class="layui-btn layui-btn-xs layui-btn-normal BatchDel"><i class="layui-icon layui-icon-delete"></i></button></td>
                                            @endif
                                        </tr>
                                        @endforeach
                                        @else
                                            <tr>
                                                <td>{{__('auth.recipientName')}}</td>
                                                <td><input type="text" name="messageName[]" maxlength="30"/></td>
                                                <td>{{__('auth.recipientTel')}}</td>
                                                <td><input type="text" name="messageTel[]" maxlength="20" /></td>
                                                <td><button type="button" class="layui-btn  layui-btn-xs layui-btn-normal KBbinning"><i class="layui-icon layui-icon-add-1"></i></button></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('auth.recipientName')}}</td>
                                                <td><input type="text" name="messageName[]" maxlength="30"/></td>
                                                <td>{{__('auth.recipientTel')}}</td>
                                                <td><input type="text" name="messageTel[]" maxlength="20"/></td>
                                                <td><button type="button" class="layui-btn layui-btn-xs layui-btn-normal BatchDel"><i class="layui-icon layui-icon-delete"></i></button></td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="establish lefth5">
                                    <h5>{{__('auth.EmailConfig')}}</h5><input type="checkbox" name="email_open" @if(isset($config['email_open']) && $config['email_open'] == 1) checked @endif
                                     value="1" lay-skin="switch" lay-text="ON|OFF">
                                    <h5>{{__('auth.whetherToPromptSuppliers')}}</h5><input type="checkbox"  @if(isset($config['email_notice_supplier']) && $config['email_notice_supplier'] == 1) checked @endif
                                     name="email_notice_supplier" value="1" lay-skin="switch" lay-text="ON|OFF">
                                    <a href="javascript:void(0)" class="layui-btn layui-btn-normal emailContent">{{__('auth.reservationContent')}}</a>
                                    <table  class="layui-table consigneeTable" lay-skin="nob">
                                        <tbody>
                                        @if(!empty($emailConsignee))
                                            @foreach($emailConsignee as $key => $val)
                                                <tr>
                                                    <td>{{__('auth.recipientName')}}</td>
                                                    <td><input type="text" name="emailName[]" maxlength="30" value="{{ isset($val['consignee_name']) ? $val['consignee_name'] : ''}}"/></td>
                                                    <td>{{__('auth.recipientMail')}}</td>
                                                    <td><input type="text" name="email[]" maxlength="30" value="{{ isset($val['consignee_email']) ? $val['consignee_email'] : ''}}"/></td>
                                                    @if($key == 0)
                                                        <td><button  type="button" class="layui-btn  layui-btn-xs layui-btn-normal KBbinningMail"><i class="layui-icon layui-icon-add-1"></i></button></td>
                                                    @else
                                                        <td><button type="button" class="layui-btn layui-btn-xs layui-btn-normal BatchDel"><i class="layui-icon layui-icon-delete"></i></button></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td>{{__('auth.recipientName')}}</td>
                                                <td><input type="text" name="emailName[]" maxlength="30"/></td>
                                                <td>{{__('auth.recipientMail')}}</td>
                                                <td><input type="text" name="email[]" maxlength="30" /></td>
                                                <td><button type="button" class="layui-btn  layui-btn-xs layui-btn-normal KBbinningMail"><i class="layui-icon layui-icon-add-1"></i></button></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('auth.recipientName')}}</td>
                                                <td><input type="text" name="emailName[]" maxlength="30"/></td>
                                                <td>{{__('auth.recipientMail')}}</td>
                                                <td><input type="text" name="email[]" maxlength="30"/></td>
                                                <td><button type="button" class="layui-btn layui-btn-xs layui-btn-normal BatchDel"><i class="layui-icon layui-icon-delete"></i></button></td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                                <div class="establish noborder">
                                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="searBtn">{{__('auth.save')}}</button>
                                    <button class="layui-btn layui-btn-danger">{{__('auth.Cancel')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="layui-tab-item">
                        <div class="chuangjian">
                            <form action="" class="layui-form">

                                <div class="establish lefth5" style="width: 940px;">
                                    <table  class="layui-table tt" lay-skin="nob">
                                        <tbody>
                                        <tr>
                                            <td></td>
                                            <td>{{__('auth.deleteExportDataCycle')}}</td>
                                            <td>
                                                <select name="cleanup_cycle">
                                                    <option value="">{{__('auth.pleaseSelect')}}</option>
                                                    @foreach(\App\Services\TaskConfigService::getCycle() as $k => $v)
                                                        <option value="{{ $k }}" @if(isset($cycle->cleanup_cycle) && $k == $cycle->cleanup_cycle) selected @endif>{{ $v }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="establish noborder">
                                    <button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="searDelBtn">{{__('auth.save')}}</button>
                                    <button class="layui-btn layui-btn-danger">{{__('auth.Cancel')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="AdditionalMes" class="layui-row" hidden style="padding-top: 20px;">
        <form action="" class="layui-form">
            <ul class="condSearch">
                <li class="layui-inline">
                    <label class="layui-form-label">{{__('auth.content')}}：</label>
                    <div class="layui-input-inline">
                        <textarea rows="10" cols="50" class="mes_text" maxlength="500" >{{ isset($config['message_notice_content']) ? $config['message_notice_content'] : ''}}</textarea>
                    </div>
                </li>
            </ul>
        </form>
    </div>
    <div id="AdditionalEmail" class="layui-row" hidden style="padding-top: 20px;">
        <form action="" class="layui-form">
            <ul class="condSearch">
                <li class="layui-inline">
                    <label class="layui-form-label">{{__('auth.content')}}：</label>
                    <div class="layui-input-inline">
                        <textarea rows="10" cols="50" class="email_text" maxlength="500" >{{ isset($config['email_notice_content']) ? $config['email_notice_content'] : ''}}</textarea>
                    </div>
                </li>
            </ul>
        </form>
    </div>
@endsection

@section('javascripts')
    <script>
        layui.use(['layer','form', 'laydate','element'], function(){
            var layer = layui.layer, form = layui.form,  element = layui.element;
            var $ = layui.jquery;

            //通知配置
            form.on('submit(searBtn)',function(data){
                var field = data.field;
                if( field.remaining_time && !field.interval_time){
                    layer.msg('{{ __("auth.ReminderIntervalRequired") }}')
                }
                if( field.remaining_time && !field.frequency){
                    layer.msg('{{ __("auth.RemindersRequired") }}')
                }
                $.MXAjax({
                    type: "get",
                    url: "/task_center/task_config/store",
                    dataType:"json",
                    data:$(".myform").serialize(),
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
            });

            //任务配置-删除周期保存
            form.on('submit(searDelBtn)',function(data){

                $.MXAjax({
                    type: "get",
                    url: "/task_center/task_config/cleanup_cycle_store",
                    dataType:"json",
                    data:data.field,
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
            });

            //添加短信预约内容
            $('.mesContent').click(function(){
                layer.open({
                    type:1,
                    title:'{{ __("auth.AddSMSReservationContent") }}',
                    shadeClose:true,
                    area:['500px','300px'],
                    content:$('#AdditionalMes'),
                    btn:['{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}'],
                    btn1: function(index, layero){
                        var content = $('.mes_text').val();
                        $("input[name='message_notice_content']").val(content);
                        layer.close(index);
                    }

                })
            });

            //添加邮件预约内容
            $('.emailContent').click(function(){
                layer.open({
                    type:1,
                    title:'{{ __("auth.AddMailReservationContent") }}',
                    shadeClose:true,
                    area:['500px','300px'],
                    content:$('#AdditionalEmail'),
                    btn:['{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}'],
                    btn1: function(index, layero){
                        var content = $('.email_text').val();
                        $("input[name='email_notice_content']").val(content);
                        layer.close(index);
                    }
                })
            });

            //添加短信收件人
            $(document).on('click', '.KBbinning', function() {
                var num =$(this).parents(".consigneeTable").find("tr").length;
                if(num >= 3){
                    layer.msg('{{ __("auth.AddUpTo3Contacts") }}') ;
                    return false;
                }
                var fx = 	'<tr>'+
                    '<td>{{ __("auth.recipientName") }}</td>'+
                    '<td><input type="text" name="messageName[]" maxlength="30"/></td>	'+
                    '<td>{{ __("auth.recipientTel") }}</td>'+
                    '<td><input type="text" name="messageTel[]" maxlength="30"/></td>'+
                    '<td><button type="button" class="layui-btn layui-btn-xs layui-btn-normal BatchDel"><i class="layui-icon layui-icon-delete"></i></button></td>'+
                    '</tr>';
                $(this).parents('tbody').append(fx);
            });

            //添加邮箱收件人
            $(document).on('click', '.KBbinningMail', function() {
                var num =$(this).parents(".consigneeTable").find("tr").length;
                if(num >= 3){
                    layer.msg('{{ __("auth.AddUpTo3Contacts") }}') ;
                    return false;
                }
                var fx = 	'<tr>'+
                    '<td>{{ __("auth.recipientName") }}</td>'+
                    '<td><input type="text" name="emailName[]" maxlength="30"/></td>	'+
                    '<td>{{ __("auth.recipientMail") }}</td>'+
                    '<td><input type="text" name="email[]" maxlength="30"/></td>'+
                    '<td><button type="button" class="layui-btn layui-btn-xs layui-btn-normal BatchDel"><i class="layui-icon layui-icon-delete"></i></button></td>'+
                    '</tr>';
                $(this).parents('tbody').append(fx);
            });
            //删除
            $(document).on('click', '.BatchDel', function() {
                var elect = $(this).parents('tr');
                layer.confirm('{{ __("auth.ConfirmDeleteOption") }}',{
                    btn : [ '{{ __("auth.Determine") }}','{{ __("auth.Cancel") }}' ],
                    yes: function(index){
                        elect.remove();
                        layer.close(index);
                    }
                })
            });

        });
    </script>
@endsection
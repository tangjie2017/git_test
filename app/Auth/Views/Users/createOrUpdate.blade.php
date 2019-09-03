<div id="createOrUpdate" class="hidden" title="{{ $title }}">
    <form action="/auth/users/doCreateOrUpdate" method="post" class = "form-horizontal", autocomplete = "off" data-ajax="true" data-ajax-method="post" data-ajax-success="success" id="createOrUpdateForm" novalidate="novalidate">
        <input type="hidden" name="Id" value="{{ isset($data['Id'])?$data['Id']:"" }}"/>
        <div class="alert alert-danger" style="display:none;">
            <p>@validatorMessage('UserCode')</p>
            <p>@validatorMessage('Password')</p>
            <p>@validatorMessage('UserName')</p>
            <p>@validatorMessage('Email')</p>
            <p>@validatorMessage('PhoneNumber')</p>
            <p>@validatorMessage('TelPhone')</p>
            <p>@validatorMessage('RoleId')</p>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right">@lang("auth.userCode")</label>
            <div class="col-sm-9">
                @if (isset($data['Id'])&&$data['Id'] > 0)
                    <input type="text" value="{{ isset($data['UserCode'])?$data['UserCode']:"" }}" name="UserCode" class="col-sm-8" readonly/><em class="require">*注：不允许修改</em>
                @else
                    <input type="text" @validator('UserCode',$validate['rules'],$validate['messages'],$validate['customAttributes']) value="{{ isset($data['UserCode'])?$data['UserCode']:"" }}" name="UserCode" class="col-sm-8"/><em class="require">*注：不允许修改</em>
                @endif
            </div>
        </div>
        @if(empty($data['Id']))
            <div class="form-group">
                <label class="col-sm-3 control-label no-padding-right">@lang("auth.password")</label>
                <div class="col-sm-9">
                    <input type="password" id="Password" name="Password" @validator('Password',$validate['rules'],$validate['messages'],$validate['customAttributes']) class="col-sm-8"/>
                </div>
            </div>
        @endif
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right">@lang("auth.userName")</label>
            <div class="col-sm-9">
                <input type="text" name="UserName" @validator('UserName',$validate['rules'],$validate['messages'],$validate['customAttributes']) value="{{ isset($data['UserName'])?$data['UserName']:"" }}" class="col-sm-8"/><em class="require">*</em>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right">@lang("auth.email")</label>
            <div class="col-sm-9">
                @if (isset($data['Id'])&&$data['Id'] > 0)
                    <input type="text" name="Email" value="{{ isset($data['Email'])?$data['Email']:"" }}" class="col-sm-8" readonly/><em class="require">*注：不允许修改</em>
                @else
                    <input type="text" name="Email" @validator('Email',$validate['rules'],$validate['messages'],$validate['customAttributes']) value="{{ isset($data['Email'])?$data['Email']:"" }}" class="col-sm-8"/><em class="require">*注：不允许修改</em>
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right">@lang("auth.phoneNumber")</label>
            <div class="col-sm-9">
                <input type="text" name="PhoneNumber" @validator('PhoneNumber',$validate['rules'],$validate['messages'],$validate['customAttributes']) value="{{ isset($data['PhoneNumber'])?$data['PhoneNumber']:"" }}" class="col-sm-8"/><em class="require">*</em>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right">@lang("auth.telPhone")</label>
            <div class="col-sm-9">
                <input type="text" name="TelPhone" @validator('TelPhone',$validate['rules'],$validate['messages'],$validate['customAttributes']) value="{{ isset($data['TelPhone'])?$data['TelPhone']:"" }}" class="col-sm-8"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right">@lang("auth.status")</label>
            <div class="col-sm-9">
                <select name="Status" class="col-sm-8">
                    <option value="1" {{ isset($data['Status'])&&$data['Status'] === 1?"selected":"" }}>@lang("auth.enable")</option>
                    <option value="0" {{ isset($data['Status'])&&$data['Status'] === 0?"selected":"" }}>@lang("auth.disabled")</option>
                </select>
            </div>
        </div>
        @if($isAdminSystem)
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right">@lang("auth.role")</label>
            <div class="col-sm-9">
                <select id="RoleId" name="RoleId" class="col-sm-8">
                    <option value="">--@lang("auth.pleaseSelect")--</option>
                    @foreach($roles as $r)
                        <option value="{{ $r['Id'] }}" {{ isset($data['RoleId'])&&$data['RoleId'] == $r['Id']?"selected":"" }}>{{ $r['Name'] }}</option>
                     @endforeach
                </select><em class="require">*</em>
            </div>
        </div>
        @endif
    </form>
</div>
<script type="text/javascript">
    $(function () {
        @if((isset($data['Id'])&&$data['Id'] > 0) == false)
            $("#Password").attr("data-val","true").attr("data-val-required","@lang("auth.passwordRequired")").parent().append('<em class="require">*</em>')
        @endif

        @if($isAdminSystem)
            $("#RoleId").attr("data-val","true").attr("data-val-required", "@lang("auth.roleRequired")")
        @endif
    })
</script>
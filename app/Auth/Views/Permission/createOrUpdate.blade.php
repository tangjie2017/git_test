<form action="/auth/permission/doCreateOrUpdate" method="post" class = "form-horizontal" data-ajax="true" data-ajax-method="post" data-ajax-begin="begin" data-ajax-success="success" novalidate="novalidate">
    <input type="hidden" name="Id" value="{{ isset($data['Id']) ? $data['Id']:"" }}"/>
    <input type="hidden" name="ParentId" id="ParentId" value="{{ isset($data['ParentId']) ? $data['ParentId']:"" }}"/>
    <div class="alert alert-danger" style="display:none;">
        <p>@validatorMessage('Name')</p>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right">@lang("auth.name")</label>
        <div class="col-sm-9">
            <input type="text" name="Name" value="{{ isset($data['Name']) ? $data['Name']:"" }}" @validator('Name',$validate['rules'],$validate['messages'],$validate['customeAttributes']) class="col-xs-10 col-sm-7"/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right">@lang("auth.resourceName")</label>
        <div class="col-sm-9">
            <input type="text" name="ResourceName" value="{{ isset($data['ResourceName']) ? $data['ResourceName']:"" }}" class="col-xs-10 col-sm-7"/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right">URL</label>
        <div class="col-sm-9">
            <input type="text" name="Url" value="{{ isset($data['Url']) ? $data['Url']:"" }}" class="col-xs-10 col-sm-7"/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right">@lang("auth.type")</label>
        <div class="col-sm-9">
            <select name="PermissionType" class="col-xs-10 col-sm-7">
                <option value="0" {{ isset($data['PermissionType'])&&$data['PermissionType']===0 ? "selected":""  }}>@lang("auth.menu")</option>
                <option value="1" {{ isset($data['PermissionType'])&&$data['PermissionType']===1 ? "selected":""  }}>@lang("auth.permission")</option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right">@lang("auth.sort")</label>
        <div class="col-sm-9">
            <input type="text" name="PermissionSort" value="{{ isset($data['PermissionSort']) ? $data['PermissionSort']:"" }}" class="col-xs-10 col-sm-7"/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right">@lang("auth.icon")</label>
        <div class="col-sm-9">
            <input type="text" name="Icon" value="{{ isset($data['Icon']) ? $data['Icon']:"" }}" class="col-xs-10 col-sm-7"/>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-3 control-label no-padding-right"></label>
        <div class="col-sm-9">
            <button type="submit" class="btn btn-primary" id="btnCreate">
                <i class="icon-ok bigger-110"></i>
                @lang("auth.commit")
            </button>
        </div>
    </div>
</form>
<script type="text/javascript">
    function begin() {
        $("#btnCreate").submitLoading(true);
    }

    function success(data) {
        $("#btnCreate").submitLoaded();
        if (data.Status == 1) {
            $.success("@lang("auth.operationSuccess")", function () {
                window.location.reload();
            });
        } else {
            $.alert(data.Message);
        }
    }
</script>
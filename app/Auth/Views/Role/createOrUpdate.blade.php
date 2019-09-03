<div id="createOrUpdate" class="hidden" title="{{ $title }}">
    <form action="/auth/role/doCreateOrUpdate" method="post" class = "form-horizontal" data-ajax="true" data-ajax-method="post" data-ajax-success="success" id="createOrUpdateForm" novalidate="novalidate">
        <input type="hidden" name="Id" value="{{ isset($data['Id'])?$data['Id']:'' }}"/>
        <div class="alert alert-danger" style="display:none;">
            <button type="button" class="close" data-dismiss="alert"></button>
            <p>@validatorMessage('Name')</p>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label no-padding-right">@lang("auth.name")</label>
            <div class="col-sm-9">
                <input type="text" name="Name" value="{{ isset($data['Name'])?$data['Name']:'' }}" @validator('Name',$validate['rules'],$validate['messages'],$validate['customeAttributes']) class="col-xs-10 col-sm-10"/><em class="require">*</em>
            </div>
        </div>
    </form>
</div>
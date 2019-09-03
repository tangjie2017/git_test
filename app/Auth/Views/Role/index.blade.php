<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="/auth/assets/css/bootstrap.min.css" rel="stylesheet" />
<link href="/auth/assets/css/font-awesome.min.css" rel="stylesheet" />
<link href="/auth/assets/css/ui.jqgrid.css" rel="stylesheet" />
<link href="/auth/assets/css/jquery-ui-1.10.3.full.min.css" rel="stylesheet" />
<link href="/auth/assets/css/ace.min.css" rel="stylesheet" />
<link href="/auth/css/auth.common.css" rel="stylesheet" />
<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <form action="/auth/role/query" id="fmSearch" class="form-inline" onsubmit="return search(this, 'grid-table')">
                {{ csrf_field() }}
                <input type="text" name="Name" class="col-xs-4 col-sm-2" placeholder="@lang("auth.name")" style="margin-right:10px;">
                <button type="submit" class="btn btn-purple btn-sm" style="margin-right:10px;">
                    <i class="icon-search icon-on-right bigger-110"></i>
                    @lang("auth.search")
                </button>
                <button class="btn btn-primary btn-sm" type="button" id="btnCreate" onclick="loadCreateOrUpdate()">
                    <i class="icon-plus icon-on-right "></i>
                    @lang("auth.create")
                </button>
            </form>
        </div>
        <div class="col-xs-12" id="grid-position">
            <table id="grid-table" autosize="true"></table>
            <div id="grid-pager"></div>
        </div>
    </div>
</div>
<div id="ddCreateOrUpdate"></div>
<div id="ddUpdateRolePermission"></div>

<script src="/auth/assets/js/jquery-2.0.3.min.js"></script>
<script src="/auth/scripts/jquery.unobtrusive-ajax.min.js"></script>
<script src="/auth/scripts/jquery.validate.min.js"></script>
<script src="/auth/scripts/jquery.validate.unobtrusive.min.js"></script>
<script src="/auth/assets/js/jqgrid/jquery.jqgrid.min.js"></script>
<script src="/auth/assets/js/jqgrid/i18n/grid.locale-en.js"></script>
<script src="/auth/assets/js/jquery-ui-1.10.3.full.min.js"></script>
<script src="/auth/scripts/auth.common.js"></script>

<link href="/auth/zTree_v3/css/zTreeStyle/zTreeStyle.css" rel="stylesheet" />
<script src="/auth/zTree_v3/js/jquery.ztree.core-3.5.js"></script>
<script src="/auth/zTree_v3/js/jquery.ztree.excheck-3.5.js"></script>
<script type="text/javascript">
    $(function () {

        var grid_selector = "#grid-table";
        var pager_selector = "#grid-pager";
        $(grid_selector).jqGrid({
            datatype: "local",
            jsonReader:{
                root:"data",
                page: "current_page",
                total: function($p){
                    return Math.ceil($p['total']/$p['per_page']);
                }
            },
            colNames: ['No', '@lang("auth.name")', '@lang("auth.operation")'],
            colModel: [
                {
                    name: 'NO', index: 'NO', width: 60, sorttype: "int", formatter: function (val, colModel, row) {
                    return colModel.rowId;
                }
                },
                { name: 'Name', width: 90 },
                {
                    name: 'Operate', index: 'Operate', width: 60, formatter: function (val, colModel, row) {
                    return "<a href='javascript:void(0)' onclick='loadUpdateRolePermission(" + row.Id + ")'>@lang("auth.permission")</a>&nbsp; | &nbsp;<a href='javascript:void(0)' onclick='loadCreateOrUpdate(" + row.Id + ")'>@lang("auth.edit")</a>&nbsp; | &nbsp;<a href='javascript:void(0)' onclick='doDelete(" + row.Id + ",\"" + row.Name.replace('"', "") + "\")'>@lang("auth.delete")</a>";
                }
                }
            ],
            viewrecords: true,
            rowNum: 10,
            rowList: [10, 20, 30],
            pager: pager_selector,
            altRows: true,
            //toppager: true,
            multiselect: true,
            //multikey: "ctrlKey",
            multiboxonly: true,
            autowidth: true
        });
    });

    function loadCreateOrUpdate(id) {
        $.post("/auth/role/createOrUpdate", { id: id }, function (data) {
            $("#ddCreateOrUpdate").html(data);
            createOrUpdate();
        })
    }

    function success(data) {
        $("#btnCreateOk").submitLoaded(true);
        if (data.Status == 1) {
            $("#createOrUpdate").dialog("destroy").remove();
            $.success("@lang("auth.operationSuccess")", function () {
                $("#fmSearch").submit();
            });
        } else {
            $.alert(r.Message);
        }
    }

    function createOrUpdate() {
        var title = $("#createOrUpdate").attr("title");
        $("#createOrUpdate").removeClass('hidden').dialog({
            resizable: false,
            modal: true,
            title: "<div class='widget-header'><h4 class='smaller'>" + title + "</h4></div>",
            title_html: true,
            buttons: [
                {
                    html: "<i class='icon-ok bigger-110'></i>&nbsp; 确定[OK]",
                    "class": "btn btn-primary btn-xs",
                    id: "btnCreateOk",
                    click: function () {
                        var result = $("#createOrUpdateForm").validate().form();
                        if(result==false){
                            $(".input-validation-error:eq(0)").focus();
                            return;
                        }
                        $("#btnCreateOk").submitLoading(true, true);
                        $(this).find("form").submit();
                    }
                }
                ,
                {
                    html: "<i class='icon-remove bigger-110'></i>&nbsp; 取消[Cancel]",
                    "class": "btn btn-xs",
                    click: function () {
                        $(this).dialog("destroy").remove();
                    }
                }
            ]
        });
    }

    function doDelete(id, name) {
        var tips = "@lang("auth.deleteConfirm")".format([name]);
        $.confirm(tips, function () {
            $.post("/auth/role/doDelete", { id: id }, function (data) {
                if (data.Status == 1) {
                    $.success("@lang("auth.deleteSuccess")", function () {
                        $("#fmSearch").submit();
                    });
                } else {
                    $.alert(data.Message);
                }
            })
        })
    }

    function loadUpdateRolePermission(id) {
        $.post("/auth/role/updateRolePermission", { id: id }, function (data) {
            $("#ddUpdateRolePermission").html(data);
            $("#ddUpdateRolePermission").removeClass('hidden').dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'>@lang("auth.permissionSet")</h4></div>",
                title_html: true,
                height: 500,
                width: 500,
                buttons: [
                    {
                        html: "<i class='icon-ok bigger-110'></i>&nbsp; 确定[OK]",
                        "class": "btn btn-primary btn-xs",
                        id: "btnURPOk",
                        click: function () {
                            $("#btnURPOk").submitLoading(true, true);
                            var treeObj = $.fn.zTree.getZTreeObj("rolePermissionTree");
                            var nodes = treeObj.getCheckedNodes(true);
                            var arrId = [];
                            for (var i = 0; i < nodes.length; i++) {
                                arrId.push(nodes[i].id);
                            }
                            var requestData = { id: $("#urpId").val(), permissionIds: arrId };
                            $.post("/auth/role/doUpdateRolePermission", requestData, function (data) {
                                $("#btnURPOk").submitLoaded(true);
                                if (data.Status == 1) {
                                    $("#ddUpdateRolePermission").dialog("close");
                                    $.success("@lang("auth.operationSuccess")");
                                } else {
                                    $.alert(r.Message);
                                }
                            });
                        }
                    }
                    ,
                    {
                        html: "<i class='icon-remove bigger-110'></i>&nbsp; 取消[Cancel]",
                        "class": "btn btn-xs",
                        click: function () {
                            $(this).dialog("close");
                        }
                    }
                ]
            });
        })
    }
</script>
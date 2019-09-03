<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="/auth/assets/css/bootstrap.min.css" rel="stylesheet" />
<link href="/auth/assets/css/font-awesome.min.css" rel="stylesheet" />
<link href="/auth/assets/css/jquery-ui-1.10.3.full.min.css" rel="stylesheet" />
<link href="/auth/assets/css/ace.min.css" rel="stylesheet" />
<link href="/auth/css/auth.common.css" rel="stylesheet" />
<style>
    body {
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }

    .ztree li span.button.add {
        margin-left: 2px;
        margin-right: 10px;
        background-position: -144px 0;
        vertical-align: top;
        *vertical-align: middle;
    }

    .ztree li span.button.icon01_ico_docu{margin-right:2px; background: url(/auth/zTree_v3/css/zTreeStyle/img/diy/3.png) no-repeat scroll 0 0 transparent; vertical-align:top; *vertical-align:middle}
</style>
<div class="page-content">
    <div class="row">
        <div class="col-xs-12">
            <button class="btn btn-primary" onclick="loadAddRootView()">
                <i class="icon-plus icon-on-right"></i>
                @lang("auth.addRootNode")
            </button>
            <div class="row">
                <div class="col-xs-12 col-sm-6 pre-scrollable table-bordered" style="max-height:800px !important;margin:10px;">
                    <ul id="permissionTree" class="ztree"></ul>
                </div>
                <div class="col-xs-12 col-sm-5" id="createOrUpdateContainer">

                </div>
            </div>
        </div>
    </div>
</div>

<script src="/auth/assets/js/jquery-2.0.3.min.js"></script>
<script src="/auth/scripts/jquery.unobtrusive-ajax.min.js"></script>
<script src="/auth/scripts/jquery.validate.min.js"></script>
<script src="/auth/scripts/jquery.validate.unobtrusive.min.js"></script>
<script src="/auth/assets/js/jquery-ui-1.10.3.full.min.js"></script>
<script src="/auth/scripts/auth.common.js"></script>

<link href="/auth/zTree_v3/css/zTreeStyle/zTreeStyle.css" rel="stylesheet" />
<script src="/auth/zTree_v3/js/jquery.ztree.core-3.5.js"></script>
<script src="/auth/zTree_v3/js/jquery.ztree.excheck-3.5.js"></script>
<script src="/auth/zTree_v3/js/jquery.ztree.exedit-3.5.js"></script>
<script type="text/javascript">
    var zNodes = {!! $znodes !!};
    $(document).ready(function () {
        $.fn.zTree.init($("#permissionTree"), {
                view: {
                    addHoverDom: addHoverDom,
                    removeHoverDom: removeHoverDom,
                    selectedMulti: false
                },
                edit: {
                    enable: true,
                    showRemoveBtn: true,
                    showRenameBtn: false
                },
                check: {
                    enable: false
                },
                data: {
                    simpleData: {
                        enable: true
                    }
                },
                callback: {
                    onClick: function (obj, treeId, treeNode) {
                        $.post("/auth/permission/createOrUpdate", {id : treeNode.id}, function (data) {
                            $("#createOrUpdateContainer").html(data);
                        })
                    },
                    beforeRemove: beforeRemove
                }
            },
            zNodes);
    });

    /*加载添加根节点视图*/
    function loadAddRootView() {
        $.get("/auth/permission/createOrUpdate", "", function (data) {
            $("#createOrUpdateContainer").html(data);
            $.validator.unobtrusive.parse(document);
        })
    }

    /*添加节点按钮*/
    function addHoverDom(treeId, treeNode) {
        var sObj = $("#" + treeNode.tId + "_span");
        if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0) return;
        var addStr = "<span class='button add' id='addBtn_" + treeNode.tId
            + "' title='@lang("auth.addChildrenNode")' onfocus='this.blur();'></span>";
        sObj.after(addStr);
        var btn = $("#addBtn_" + treeNode.tId);
        if (btn) btn.bind("click", function () {
            $.get("/auth/permission/createOrUpdate", "", function (data) {
                $("#createOrUpdateContainer").html(data);
                $("#ParentId").val(treeNode.id)
            })
            return false;
        });
    };

    /*移除节点添加按钮*/
    function removeHoverDom(treeId, treeNode) {
        $("#addBtn_" + treeNode.tId).unbind().remove();
    };

    /*加载添加子节点视图*/
    function loadAddChildren(treeId, treeNode) {
        $.get("/auth/permission/createOrUpdate", "", function (data) {
            $("#createOrUpdateContainer").html(data);
            $("#ParentId").val(treeNode.ParentId);
            $.validator.unobtrusive.parse(document);
        })
    }

    /*删除节点*/
    function beforeRemove(treeId, treeNode) {
        var tips = "@lang("auth.deleteConfirm")".format([treeNode.name]);
        $.confirm(tips, function () {
            $.loading();
            $.post("/auth/permission/doDelete", { id: treeNode.id }, function (data) {
                $.loading(true);
                if(data.Status == 1){
                    $.success("@lang("auth.deleteSuccess")",function(){
                        window.location.reload();
                    })
                }else{
                    $.alert(data.Message);
                }
            }, "json");
        });

        return false;
    }
</script>
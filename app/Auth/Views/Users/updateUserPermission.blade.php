<ul id="userPermissionTree" class="ztree"></ul>
<input type="hidden" id="urpId" value="{{ $id }}" />
<script type="text/javascript">
    var zNodes = {!! $znodes !!};
    $(document).ready(function () {
        $.fn.zTree.init($("#userPermissionTree"), {
                check: {
                    enable: true,
                    chkboxType: { "Y": "ps", "N": "ps" }
                },
                data: {
                    simpleData: {
                        enable: true
                    }
                },
                callback: {
                    beforeCheck: function (treeId, treeNode) {
                        var zTree = $.fn.zTree.getZTreeObj("userPermissionTree");
                        if(treeNode.children && treeNode.children.length<=1){
                            zTree.setting.check.chkboxType = { "Y" : "p", "N" : "ps" };;
                        }
                        else
                        {
                            zTree.setting.check.chkboxType = { "Y" : "ps", "N" : "ps" };
                        }
                    },
                    onClick: function (obj, treeId, treeNode) {

                    }
                }
            },
            zNodes);
    });
</script>
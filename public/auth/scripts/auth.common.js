$(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        contentType: "application/x-www-form-urlencoded;charset=utf-8",
        complete: function (XMLHttpRequest, textStatus) {
            if (XMLHttpRequest.status == "499") {
                $.alert("登录过期，请重新登录", function () {
                    if (window.parent) {
                        window.parent.location.reload();
                    } else {
                        window.location.reload();
                    }
                });
            }else if (XMLHttpRequest.status == "401") {
                $.alert("用户没有权限执行此操作");
            }else if (XMLHttpRequest.status == "500") {
                $.alert("系统内部错误，请联系系统管理员");
            }
        }
    });
    $(document).ajaxSuccess(function (event, req, options) {
        $.validator.unobtrusive.parse(document); //验证
    });
});
(function ($) {
    //override dialog's title function to allow for HTML titles
    $.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
        _title: function (title) {
            var $title = this.options.title || '&nbsp;'
            if (("title_html" in this.options) && this.options.title_html == true)
                title.html($title);
            else title.text($title);
        }
    }));
    $.validator.setDefaults({
        showErrors: function () {
            var i, elements;
            if (this.errorList.length > 0) {
                var alertDanger = $(this.errorList[0].element).parents('form').find(".alert-danger");
                if (alertDanger.length > 0) {
                    alertDanger.show();
                }
            }
            for (i = 0; this.errorList[i]; i++) {
                var error = this.errorList[i];
                if (this.settings.highlight) {
                    this.settings.highlight.call(this, error.element, this.settings.errorClass, this.settings.validClass);
                }
                this.showLabel(error.element, error.message);
            }
            if (this.errorList.length) {
                this.toShow = this.toShow.add(this.containers);
            }
            if (this.settings.success) {
                for (i = 0; this.successList[i]; i++) {
                    this.showLabel(this.successList[i]);
                }

                var alertDanger = $(this.successList[0]).parents('form').find(".alert-danger");
                if (alertDanger.length > 0) {
                    var error = alertDanger.find(".field-validation-error");
                    if (error.length == 0) {
                        alertDanger.hide();
                    }
                }
            }
            if (this.settings.unhighlight) {
                for (i = 0, elements = this.validElements() ; elements[i]; i++) {
                    this.settings.unhighlight.call(this, elements[i], this.settings.errorClass, this.settings.validClass);
                }
            }
            this.toHide = this.toHide.not(this.toShow);
            this.hideErrors();
            this.addWrapper(this.toShow).show();
        }
    });
    $.extend($, {
        confirm: function (text, okFun) {
            var $win = $("<div><p class='bigger-150 bolder center grey' style='margin-top:10px;word-break:break-all'><i class='icon-hand-right blue bigger-120'></i>&nbsp;" + text + "</p></div>");
            $("body").append($win)
            $win.dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'> Confirm</h4></div>",
                title_html: true,
                buttons: [
                    {
                        html: "<i class='icon-ok bigger-110'></i>&nbsp; 确定[OK]",
                        "class": "btn btn-success btn-xs",
                        click: function () {
                            $(this).dialog("destroy").remove();
                            if ($.isFunction(okFun)) {
                                okFun();
                            }
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
            $win.parents(".ui-dialog").addClass("ui-tips-dialog");
            $win.parents(".ui-dialog").prev().addClass("ui-tips-front");
        },
        success: function (text, okFun) {
            var $win = $("<div><p class='bigger-150 bolder center grey' style='margin-top:10px;word-break:break-all'><i class='icon-ok bigger-110 green'></i>&nbsp;" + text + "</p></div>");
            $("body").append($win)
            $win.dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'>Tips</h4></div>",
                title_html: true,
                buttons: [
                    {
                        html: "<i class='icon-ok bigger-110'></i>&nbsp; 确定[OK]",
                        "class": "btn btn-success btn-xs",
                        click: function () {
                            $(this).dialog("destroy").remove();
                            if ($.isFunction(okFun)) {
                                okFun();
                            }
                        }
                    }
                ]
            });
            $win.parents(".ui-dialog").addClass("ui-tips-dialog");
            $win.parents(".ui-dialog").prev().addClass("ui-tips-front");
        },
        alert: function (text, okFun) {
            var $win = $("<div><p class='bigger-150 bolder center grey' style='margin-top:10px;word-break:break-all'><i class='icon-warning-sign red'></i>&nbsp;" + text + "</p></div>");
            $("body").append($win)
            $win.dialog({
                resizable: false,
                modal: true,
                title: "<div class='widget-header'><h4 class='smaller'>Tips</h4></div>",
                title_html: true,
                buttons: [
                    {
                        html: "<i class='icon-ok bigger-110'></i>&nbsp; 确定[OK]",
                        "class": "btn btn-success btn-xs",
                        click: function () {
                            $(this).dialog("destroy").remove();
                            if ($.isFunction(okFun)) {
                                okFun();
                            }
                        }
                    }
                ]
            });
            $win.parents(".ui-dialog").addClass("ui-tips-dialog");
            $win.parents(".ui-dialog").prev().addClass("ui-tips-front");
        },
        /*
        #stop: 默认false
        */
        loading: function (stop) {
            $("#dom_loading").remove();
            if (!stop) {
                $modal = $("<div id='dom_loading' class='ui-widget-overlay ui-front' style='text-align:center;z-index:100000 !important;'><i class='icon-spinner icon-spin icon-5x white' style='position:fixed;top:50%'></i></div>");
                $("body").append($modal);
            }
        }
    });

    $.extend($.fn, {
        submitLoading: function (disabled, isForDiloag) {
            if (isForDiloag) {
                $(this).find(".ui-button-text").append('<i class="icon-spinner icon-spin icon-1x white"></i>');
            } else {
                $(this).append('<i class="icon-spinner icon-spin icon-1x white"></i>');
            }
            if (disabled) {
                $(this).attr("disabled", true);
            }
        },
        submitLoaded: function (isForDiloag) {
            if (isForDiloag) {
                $(this).find(".ui-button-text").find(".icon-spin").remove();
            } else {
                $(this).find(".icon-spin").remove();
            }
            $(this).removeAttr("disabled");
        }
    });
}(jQuery));


String.prototype.format = function (args) {
    var reg = /{(\d+)}/gm;
    return this.replace(reg, function (match, name) {
        return args[~~name];
    });
}

Date.prototype.format = function (format) {
    var o = {
        "M+": this.getMonth() + 1, //month 
        "d+": this.getDate(), //day 
        "h+": this.getHours(), //hour 
        "m+": this.getMinutes(), //minute 
        "s+": this.getSeconds(), //second 
        "q+": Math.floor((this.getMonth() + 3) / 3), //quarter 
        "S": this.getMilliseconds() //millisecond 
    }

    if (/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
    }

    for (var k in o) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1 ? o[k] : ("00" + o[k]).substr(("" + o[k]).length));
        }
    }
    return format;
}

function search(formId, tbId) {
    var tb = $(tbId);
    tb = tb.length == 0 ? $("#" + tbId) : tb;
    var form = $(formId);
    form = form.length == 0 ? $("#" + formId) : form;
    var isButtonTrigger = true;
    tb.jqGrid('setGridParam', {
        url: form.attr("action"),
        datatype: 'json',
        mtype: 'post',
        loadComplete: function () {
            updatePagerIcons(this);
        },
        serializeGridData: function (postData) {
            if (isButtonTrigger) {
                postData['page'] = 1;
                isButtonTrigger = false;
            }

            var formData = form.serializeArray();
            for (var i = 0; i < formData.length; i++) {
                postData[formData[i]['name']] = formData[i]['value'];
            }

            return postData;
        }
    }).trigger("reloadGrid");
    return false;
}

//replace icons with FontAwesome icons like above
function updatePagerIcons(table) {
    var replacement =
    {
        'ui-icon-seek-first': 'icon-double-angle-left bigger-140',
        'ui-icon-seek-prev': 'icon-angle-left bigger-140',
        'ui-icon-seek-next': 'icon-angle-right bigger-140',
        'ui-icon-seek-end': 'icon-double-angle-right bigger-140'
    };
    $('.ui-pg-table:not(.navtable) > tbody > tr > .ui-pg-button > .ui-icon').each(function () {
        var icon = $(this);
        var $class = $.trim(icon.attr('class').replace('ui-icon', ''));

        if ($class in replacement) icon.attr('class', 'ui-icon ' + replacement[$class]);
    })
}
$.extend({
    "MXAjax": function (settings) {
        var url = settings.url;
        var type = settings.type;
        var dataType = settings.dataType;
        var data = settings.data;
        $.ajax({
            url: url,
            type: type,
            dataType: dataType,
            data: data,
            success: function (data) {
                if (data.Status == 2) { //未登录
                    alert(data.Message);
                    location.href = '/';
                    return;
                }

                if (data.Status == 3) { //没有权限
                    alert(data.Message);
                    return;
                }

                if (data.Status == 4) { //toke过期
                    alert(data.Message);
                    window.location.reload();
                    return;
                }

                var s = settings.success;
                if (typeof s != 'undefined' && s instanceof Function) {
                    s(data);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                var e = settings.error;
                if (typeof e != 'undefined' && e instanceof Function) {
                    e(XMLHttpRequest, textStatus, errorThrown);
                }
            }
        });
    } ,
    "escapeHTML": function (html) {
        html = "" + html;
        return html.replace(new RegExp('<', "g"), '&lt;').replace(new RegExp('>', "g"), '&gt;');
    }
});

/*
    将字符串转换成时间对象后再加时区, window.timeZone带符号
 */
String.prototype.FormatToDate = function (fmt) {
    if (fmt == null || fmt == undefined || fmt == ''){
        return '';
    }

    var time = new Date(this);
    var timeZone = parseInt(window.timeZone) == 8 ? 0:parseInt(window.timeZone);
    time.setHours(time.getHours() + timeZone);
    var o = {
        "M+": time.getMonth() + 1, //月份
        "d+": time.getDate(), //日
        "h+": time.getHours(), //小时
        "m+": time.getMinutes(), //分
        "s+": time.getSeconds(), //秒
        "q+": Math.floor((time.getMonth() + 3) / 3), //季度
        "S": time.getMilliseconds() //毫秒
    }

    if (/(y+)/.test(fmt))
        fmt = fmt.replace(RegExp.$1, (time.getFullYear() + "").substr(4 - RegExp.$1.length));
    for (var k in o){
        if (new RegExp("(" + k + ")").test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
        }
    }

    return fmt;
}
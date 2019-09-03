// 获取当前时间
function getNowFormatDate() {
    $.MXAjax({
        url:'getTime',
        type:'get',
        data:{},
        dataType:'json',
        success:function (time) {
            var oldTimeZone = $('#oldTimeZone');
            if (temp == '') {
                window.temp = $('.timeZone').eq(0).text();
            }
            if(oldTimeZone.attr('data') == '8') {
                $('.nowdate p').text($.getMessage('SwitchTo') + ' [' + oldTimeZone.val() + '（' + oldTimeZone.attr('data') + '）] ' + $.getMessage('TimeZone')+':  '  + time.utc_8);
                $('.timeZone').eq(0).text(window.temp + time.warehouse);
            }else{
                $('.nowdate p').text($.getMessage('SwitchTo') + ' [' + oldTimeZone.val() + '（' + oldTimeZone.attr('data') + '）] ' + $.getMessage('TimeZone')+':  '   + time.warehouse);
                $('.timeZone').eq(0).text(window.temp + time.utc_8);
            }


        }
    })
}

window.temp = '';
var firstLogin = true;
$(function () {
    getNowFormatDate();
    setInterval(getNowFormatDate, 60000);
})

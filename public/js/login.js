$(function () {
    //判断是否在iframe中
    if(self != top) {
        window.top.location = '/';
    }

    //验证用户
    $("#doLogin").on('click', function () {
        doLogin();
    });

    //回车登录事件
    $('body').bind('keyup', function (event) {
        if (event.keyCode == "13") {
            doLogin();
        }
    });

    //点击按钮查询
    $('.multLable em').click(function(){
        var ro = $(this).attr('data-id');
        $('#wareCode').val(ro);
        $.MXAjax({
           type:'post',
            dataType: 'json',
            url: '/auth/bisLogin',
            data: $('#myForm').serialize(),
            success :function (response) {
                layer.closeAll();
                if (response.Status) {
                    window.location.href = response.Data.redirect;
                }
            }

        });

    })

    //退出操作
    $('#doLogout').on('click', function () {
        layer.confirm($.getMessage('logout'), {
            btn: [$.getMessage('confirm'), $.getMessage('cancel')],
            title: $.getMessage('tip')
        }, function () {
            window.location.href = '/logout';
        });
    });

    function doLogin() {
        var userCode = $('input[name="userCode"]').val(),
            password = $('input[name="password"]').val();

        if (userCode == '') {
            $('.remind').html($.getMessage('userCodeEmpty'));
            return false;
        }

        if (password == '') {
            $('.remind').html($.getMessage('passwordEmpty'));
            return false;
        }

        //改变样式
        $('#doLogin').html($.getMessage('loggingIn')).addClass('disabled');
        //去掉提示
        $('.remind').html('');

        $.MXAjax({
            type: 'post',
            dataType: 'json',
            url: '/auth/doLogin',
            data: $('#myForm').serialize(),
            success: function success(response) {
                if(response.Message == $.getMessage('SelectWarehouse')){
                     layer.open({
                       type: 1,
                       title: $.getMessage('SelectWarehouse'),
                       area: ['300px', '200px'],
                       content: $('#warehouse'),
                });
            }

            $('.remind').html(response.Message);
            if (response.Status) {
                window.location.href = response.Data.redirect;
            }
            //去掉样式
            $('#doLogin').html($.getMessage('login')).removeClass('disabled');
            },
            error: function error(XMLHttpRequest, textStatus, errorThrown) {
                $('.remind').html(textStatus);
                //去掉样式
               $('#doLogin').html($.getMessage('login')).removeClass('disabled');
            }
        });
    }
});

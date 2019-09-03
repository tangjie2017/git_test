<?php

return [
    //线上为true,测试&开发为false
    'app_model' => false,

    //谷仓API
    'wmsOms' => [
        //API密钥
        'appToken' => 'df3c89455ff73a1d67d7ad5ad6598eb3',
        //API标识
        'appKey' => 'dea70dff3ce494c965346de0199086cc',
        //正式环境
        'appUrl' => 'http://wms.goodcang.com:60081/default/customer-api',
        //测试环境
        'testAppUrl' => 'http://192.168.109.172:60602/default/customer-api',
        //Aes类，加密-解密密钥
        'secretKey' => '3171613277733472663365647A786376',

        //获取用户
        'wmsUser'=> 'http://wms.goodcang.com:60081/default/wms-api/call-server?server=getUserListForBis',
        'testWmsUser'=> 'http://192.168.106.70:6868/default/wms-api/call-server?server=getUserListForBis',
        'newWmsUser'=> 'http://192.168.109.170:61601/default/wms-api/call-server?server=getUserListForBis',
        'lineWmsUser'=> 'http://wms.goodcang.com:60081/default/wms-api/call-server?server=getUserListForBis',
        //用户登录
        'userLogin'=>'http://wms.goodcang.com:60081/default/wms-api/call-server?server=verificationBisLogin',
        'testUserLogin'=>'http://192.168.106.70:6868/default/wms-api/call-server?server=verificationBisLogin',

        //美西仓库
        'USWE' => 'http://owms_uswe.goodcang.com:60081/restapi/delivery',
        //美东仓库
        'USEA' => 'http://owms_usea.goodcang.com:60081/restapi/delivery',

        //OMWS接口 美西仓库
        'testUSWE' => 'http://sbx-owms-uswe.eminxing.com:60080/restapi/delivery',
        'testUSEA' => 'http://sbx-owms-usea.eminxing.com:60080/restapi/delivery',
        //wms
        'wmsKey' => '167fbbc91b740b1421edc5cdd47389ea',
        'testInboundUrl' => 'http://sbx-wms.eminxing.com:60080/default/wms-api/call-server?server=getReceivingDetailListForBIS',
        'inboundUrl' => '',
    ],

    //owms
    'owms' => [
        //API密钥
        'appToken' => 'owms666666',
    ],

    //过滤用户绑定仓库
    'warehouse' => [
        'USEA','USWE'
    ],

    //fbg
    'fbg' => [
        //系统码
        'pdaAppId' => '95a8cd09b0cb49d7aa2b0c27303e6bee',
        'appId' => '5153369b2c0b4cd1aa45cb8ede6e52ce',
        //获取令牌
        'testAuth' => 'https://sbx-fbg.eminxing.com/abutment/auth',
        //登录页
        'login'=> 'https://sbx-fbg.eminxing.com/login',
        'pdaLogin'=> 'https://sbx-fbg.eminxing.com/pda/login',
        //获取用户
        'getUser' => 'https://sbx-fbg.eminxing.com/abutment/getUserList',
        //验证登录
        'openLogin'=> 'https://sbx-fbg.eminxing.com/abutment/openLogin',
        'UserCode'=>'gcwms',
        'Password'=>'Td147852',
    ],

    //线上fbg
//    'fbg' => [
//        'lineWmsUser'=> 'http://wms.goodcang.com:60081/default/wms-api/call-server?server=getUserListForBis',
//        //系统码
//        'pdaAppId' => 'b21f378cedf84766b11cba8172927223',
//        'appId' => 'b844ea76e3b343349c00eda62c7a82fd',
//        //获取令牌
//        'testAuth' => 'https://fbg.goodcang.com/abutment/auth',
//        //登录页
//        'login'=> 'https://fbg.goodcang.com/login',
//        'pdaLogin'=> 'https://fbg.goodcang.com/pda/login',
//        //获取用户
//        'getUser' => 'https://fbg.goodcang.com/abutment/getUserList',
//        //验证登录
//        'openLogin'=> 'https://fbg.goodcang.com/abutment/openLogin',
//        'UserCode'=>'gcwms',
//        'Password'=>'Td147852',
//    ],

];
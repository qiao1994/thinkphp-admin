<?php
return array(
	//'配置项'=>'配置值'
	//支付宝配置
	'ALIPAY_CONFIG' => array(
		'partner' => '2088421597394473', //合作者身份
		'seller_id' => '2088421597394473', //一般同合作者身份
		'key' => '5vdn51l5upgqfdivvi5mdvubzvove6nf', //密钥key
		'notify_url' => 'http://'.$_SERVER['SERVER_NAME'] . '/Api/Alipay/notifyUrl', //异步通知URL
		'return_url' => 'http://'.$_SERVER['SERVER_NAME'] . '/Api/Alipay/returnUrl', //页面跳转同步通知URL
		//'notify_url' => 'http://'.$_SERVER['SERVER_NAME'] . '/alipay/notify_url.php', //异步通知URL
		//'return_url' => 'http://'.$_SERVER['SERVER_NAME'] . '/alipay/return_url.php', //页面跳转同步通知URL
		'sign_type' => strtoupper('MD5'),
		'input_charset' => strtolower('utf-8'),
		'cacert' => getcwd() . '/App/Api/Common/cacert.pem',
		'transport' => 'http',
		'payment_type' => '1',
		'service' => 'alipay.wap.create.direct.pay.by.user', //类型
	),
    //微信配置
    'WX_CONFIG' => array(
        'APPID' => 'wx33e3b0e193798e4f', //APPI
        'MCHID' => '1374027202', //商户ID
        'KEY' => '3487yif4f7f8hweuhf9w3802hf23h8f2', //KEY
        'APPSECRET' => '67fa477fbf6882f52fb20b7484b98212', //APPSECRET
        'LOGINCALLBACK' => 'http://'.$_SERVER['SERVER_NAME']. '/Api/Wxlogin/callback', //微信登录回调
        'JS_API_CALL_URL' => 'http://'.$_SERVER['SERVER_NAME']. '/Api/Wxpay/callback', //jsapi支付回调
        'NOTIFY_URL' => 'http://'.$_SERVER['SERVER_NAME']. '/Api/Wxpay/notify', //jsapi支付异步通知
        'SSLCERT_PATH' => '/web/app/www.116lyw.com/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_cert.pem', // 证书位置
        'SSLKEY_PATH' => '/web/app/www.116lyw.com/ThinkPHP/Library/Vendor/WxPayPubHelper/cacert/apiclient_key.pem', //证书位置
        'COMPANY' => '116',
    ),
);

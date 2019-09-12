<?php
return array(
	//数据库配置信息
	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => 'localhost', // 服务器地址
	'DB_NAME' => 'db1', // 数据库名
	'DB_USER' => 'xxx', // 用户名
	'DB_PWD' => 'xxxx', // 密码
	'DB_PORT' => 3306, // 端口
	'DB_PREFIX' => 'db_', // 数据库表前缀
	'DB_CHARSET' => 'utf8', // 字符集
	'DB_DEBUG' => false, // 数据库调试模式 开启后可以记录SQL日志 3.2.3新增
	//表单令牌
    	'TOKEN_ON' => true,
	'URL_MODEL' => 2, // URL访问模式
	'PAGE_NUM' => 100,
	'SHOW_TRACE_PAGE' => false,
	// 配置邮件发送服务器
	'MAIL_HOST' => 'smtp.163.com', //smtp服务器的名称
	'MAIL_SMTPAUTH' => TRUE, //启用smtp认证
	'MAIL_USERNAME' => '***@**.com', //邮箱名
	'MAIL_FROM' => '***@**.com', //发件人地址
	'MAIL_FROMNAME' => '发件人姓名', //发件人姓名
	'MAIL_PASSWORD' => '******', //邮箱密码
	'MAIL_CHARSET' => 'utf-8', //设置邮件编码
	'MAIL_ISHTML' => TRUE, // 是否HTML格式邮件

	/* admin后台的设置 */
    	//******NOT_AUTO_GENERATE 前端文件是否需要实时生成
	//true 所有全都不生成
	//false 所有都生成
	//不需要生成的controller或action(如果指定了action则指定action不生成，如果未指定action则整个controller都不生成)
	// array [
	// 	module=>[
	// 		controller=>[
	//      		action1,
	//      		action2,
	//  		],
	// 	],
	// ]
	'NOT_AUTO_GENERATE' => [
	],
	// 'NOT_AUTO_GENERATE' => true, //设置为true则所有都不实时更新
);

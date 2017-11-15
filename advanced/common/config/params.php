<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
	//接口签名key
	"interfacekey" => "ddz2017xlt",
	//快递公司官方电话
	'expressTel'=>[
		'huitongkuaidi'=>'95320',
		'kuayue'=>'400-809-8098',
		'shunfeng'=>'95338',
		'yuantong'=>'95554',
		'zhongtong'=>'95311',
		'shentong'=>'95543',
		'ems'=>'11183',
		'yunda'=>'95546',						
		'youzhengguonei'=>'11183',			
	],
	//快递公司
	'expressCompany'=>[
		'huitongkuaidi'=>'百世汇通快递',
		'kuayue'=>'跨越物流',
		'shunfeng'=>'顺丰速运',
		'yuantong'=>'圆通快递',
		'zhongtong'=>'中通快递',
		'shentong'=>'申通快递',
		'ems'=>'EMS',
		'yunda'=>'韵达快递',
		'kuaijiesudi'=>'快捷速递',
		'youzhengguonei'=>'邮政国内',
	],	
	"companyId"=>"100000",
	//产品价格
	"price"=>199,
	//二级分销奖励配置
	"reward"=>[
		1=>70,
		2=>30,
	],
	//项目域名配置,小程序接口请求地址，必须是https
	"miniUrl"=>"https://xlt.n51888.com",
	"appUrl"=>"http://xlt.n51888.com",
	//测试账号
	"testAccount"=>[
		"wx_public"=>[
			"100000",//admi
			
			"100002", //小豪
			"100003",//王晓萍
			"100004",//唐剑
			"100005",//畅爷
			"100006",//希希 王道系
			"100008",//赵灵通
			"100036",//直树
			"100044",  //晓晓
			"100047",  //晓晓
			"100180",//何潇
			"100226",
			"101557",//王道系统 授权号
			"101544",//王道系统物流号
		],
	],
];

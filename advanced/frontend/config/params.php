<?php
return [
    'adminEmail' => 'admin@example.com',
	//公众号配置
	'token' => 'atoken2017xlt',
	'appid' => 'wx203a3648cb78d8bb',
	'secret' => '7df5e0daa7c68804037da58d9df6b64a',
	'mchid' => '1488015882',
	'key' => '2017dongdaozhuwdxueliantiewxpubl',	
	'webTokenPath' => dirname(__FILE__).'/../cache/token',
	'timePath' => dirname(__FILE__).'/../cache/expire',
	'jsTicketPath' => dirname(__FILE__).'/../cache/jsapi_ticket.json',

	//缩略图用户头像目录	
	'miniAvatarPath'=>dirname(__FILE__).'/../web/images/miniAvatars/',
    //缩略图用户头像外网地址
    'outPath'=>'http://xlt.n51888.com'.'/images/miniAvatars/',
	//推荐二维码目录
	'myQrCodePath'=>dirname(__FILE__).'/../web/images/myqrcode/',
	'guanzuUrl'=>'http://mp.weixin.qq.com/s/xOw3NcF3F7bxvYS1Tsy9rA',  
];

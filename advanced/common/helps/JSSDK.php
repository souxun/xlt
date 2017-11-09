<?php
namespace common\helps;

use Yii;
use common\helps\Weixin;

class JSSDK 
{
	private $appId;
	private $appSecret;
	private $webTokenPath;
	private $timePath;
	private $jsTicketPath;

	public function __construct() 
	{
		$this->appId = Yii::$app->params['appid'];//appid
		$this->appSecret = Yii::$app->params['secret'];//secret
		$this->webTokenPath = Yii::$app->params['webTokenPath'];//webtoken的路径
		$this->timePath = Yii::$app->params['timePath'];//过期时间
		$this->jsTicketPath = Yii::$app->params['jsTicketPath'];
	}

	public function getSignPackage($url = null) 
	{
		$jsapiTicket = $this->getJsApiTicket();
		//html的静态页面在前端通过ajax将url传到后台签名，前端需要用js获取当前页面除去'#'hash部分的链接
		if(empty($url))
		{
			// 注意 URL 一定要动态获取，不能 hardcode.
			$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
			$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";	
		}

		$timestamp = time();
		$nonceStr = $this->createNonceStr();

		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

		$signature = sha1($string);

		$signPackage = array(
		  "appId"     => $this->appId,
		  "nonceStr"  => $nonceStr,
		  "timestamp" => $timestamp,
		  "url"       => $url,
		  "signature" => $signature,
		  "rawString" => $string
		);
		return $signPackage; 
	}

	private function createNonceStr($length = 16) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for ($i = 0; $i < $length; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	private function getJsApiTicket() 
	{
		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = json_decode(file_get_contents($this->jsTicketPath));
		if ($data->expire_time < time()) 
		{
			$Weixin = new Weixin('');
			
			$Weixin->haveAccessToken();
			$accessToken = $Weixin->webToken;
			$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
			$res = json_decode($this->httpGet($url));
			$ticket = $res->ticket;
			if ($ticket) {
				$data->expire_time = time() + 7000;
				$data->jsapi_ticket = $ticket;
				$fp = fopen($this->jsTicketPath, "w");
				fwrite($fp, json_encode($data));
				fclose($fp);
			}
		} else {
			$ticket = $data->jsapi_ticket;
		}

		return $ticket;
	}

	private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);

		$res = curl_exec($curl);
		curl_close($curl);

		return $res;
	}
}
?>




<?php
//常用方法  类
class MainUse 
{
	private $appid;
	private $secret;
	private $openurl;
	private $ak = "FE70eb7ce52aa9fa527c45e8d3a370be";
	private $addressUrl = "http://api.map.baidu.com/geocoder/v2/";
	private $chgUrl = "http://api.map.baidu.com/geoconv/v1/";
	
	function __construct(){
		$this->appid=Yii::$app->params['appid'];
		$this->secret=Yii::$app->params['secret'];
		$this->openurl=Yii::$app->params['openurl'];
	}
	
	public function oauthUrl($rurl,$state)
	{
		$rurl = Yii::$app->params['wxUrl'].'/C/'.$rurl.'.html';
		$rurl = urlencode($rurl);
		return $this->openurl.$this->appid.'&redirect_uri='.$rurl.'&response_type=code&scope=snsapi_base&state='.$state.'#wechat_redirect';
	}
	
	//微信网页授权验证 推荐二维码
	public function qrcodeAuthUrl($rurl,$state){
		$rurl = urlencode($rurl);
		return $this->openurl.$this->appid.'&redirect_uri='.$rurl.'&response_type=code&scope=snsapi_base&state='.$state.'#wechat_redirect';
	}
	
	public function chgLocation($lat,$lng)
	{
		$url=$this->chgUrl."?coords=".$lat.','.$lng.'&from=3&to=5&ak='.$this->ak;
		$result=$this->getClient($url);
		//stdClass Object ( [status] => 0 [result] => Array ( [0] => stdClass Object ( [x] => 30.247682028181 [y] => 119.98626773154 ) ) )
        //$result=json_decode($result);
		$re=array();
		if (is_object($result) && $result->status==0){
			$re['lat']=$result->result[0]->x;
			$re['lng']=$result->result[0]->y;
		}else {
			$re['lat']=$lat;
			$re['lng']=$lng;
		}
		return $re;
	}
	
	public function GetAddress($lat,$lng){//用坐标获取地址信息
		$url = $this->addressUrl."?location=".$lat.','.$lng.'&output=json&pois=0&ak='.$this->ak;
		$result = $this->getClient($url);
		//result=stdClass Object ( [status] => 0 [result] => stdClass Object ( [location] => stdClass Object ( [lng] => 119.98019402792 [lat] => 30.241545096099 ) [formatted_address] => 浙江省杭州市余杭区闲林西路 [business] => 闲林 [addressComponent] => stdClass Object ( [city] => 杭州市 [district] => 余杭区 [province] => 浙江省 [street] => 闲林西路 [street_number] => ) [cityCode] => 179 ) )
// 		$result=json_decode($result);
		$re='';
		if ($result->status==0){
			$re=$result->result->addressComponent;
		}
		return $re;
	}
	
	public function getClient($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);//
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// 使用自动跳转
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Errno'.curl_error($ch);//捕抓异常
			return;
		}
		curl_close($ch);
		$output = json_decode($output);
		return $output;
	}

	
	public function postClient($url,$post_data){//POST方法
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);//
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// 使用自动跳转
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Errno'.curl_error($ch);//捕抓异常
			return;
		}
		curl_close($ch);
		$output=json_decode($output);
		return $output;
	}
}

?>
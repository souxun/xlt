<?php
namespace common\helps;

use Yii;
use yii\log\FileTarget;
use common\helps\SimpleImageHandle;

/*
* 公共方法类
*/
class PublicFunction
{
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
			Yii::error('get_Errno'.curl_error($ch));//捕抓异常
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
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// 使用自动跳转
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			Yii::error('post_Errno'.curl_error($ch));//捕抓异常
			return;
		}
		curl_close($ch);
		$output = json_decode($output);
		return $output;
	}	
	
	//验证身份证是否有效
	public function validateIDCard($IDCard) {
		if (strlen($IDCard) == 18) {
			return $this->check18IDCard($IDCard);
		} elseif ((strlen($IDCard) == 15)) {
			$IDCard = $this->convertIDCard15to18($IDCard);
			return $this->check18IDCard($IDCard);
		} else {
			return false;
		}
	}

	//计算身份证的最后一位验证码,根据国家标准GB 11643-1999
	private function calcIDCardCode($IDCardBody) {
		if (strlen($IDCardBody) != 17) {
			return false;
		}

		//加权因子 
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		//校验码对应值 
		$code = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		$checksum = 0;

		for ($i = 0; $i < strlen($IDCardBody); $i++) {
			$checksum += substr($IDCardBody, $i, 1) * $factor[$i];
		}

		return $code[$checksum % 11];
	}

	//验证手机号
	public function validatePhone($phone)
	{
		if(preg_match("/^1[34578]{1}\d{9}$/",$phone)){  
			return true;  
		}else{  
			return false;  
		} 	
	}
	
	/*
	* microsecond 微秒     millisecond 毫秒
	*返回时间戳的毫秒数部分
	*/ 
	public function get_millisecond() 
	{ 
		list($usec, $sec) = explode(" ", microtime()); 
		$msec = round($usec*1000); 
		return $msec; 			
	}		
	
	//生成3rd_session
	public function getSessionId($user_id)
	{
		$session_id = `head -n 80 /dev/urandom | tr -dc A-Za-z0-9 | head -c 168`;
		return $user_id.$session_id;
	}

	//生成随机4位数字验证码
	public function getCheckcode()
	{
		$num = '';
		for($i=0;$i<4;$i++)
		{
			$num .= mt_rand(0,9);
		}
		return $num;
	}	
	
	//支付记录日志
	public function savePayLog($file,$msg)
	{
		$log = new FileTarget();
		$log->logFile = Yii::$app->getRuntimePath() . '/logs/'.$file;
		$log->messages[] = [$msg,1,'application',microtime(true)];
		$log->export();		
	}	
	
	//微支付小程序、app配置文件重新生成
	public function resetWxpayConfig($appid,$mchid,$key,$appsecret)
	{
		$path = __DIR__ .'/wxPay/lib/WxPay.Config.php';
		$myfile = fopen($path, "w");
		$txt = "<?php\n";
		$txt .= "class WxPayConfig\n";
		$txt .= "{\n";
		$txt .= "const APPID = '".$appid."';\n";
		$txt .= "const MCHID = '".$mchid."';\n";
		$txt .= "const KEY = '".$key."';\n";
		$txt .= "const APPSECRET = '".$appsecret."';\n";
		$txt .= "const SSLCERT_PATH = '../cert/apiclient_cert.pem';\n";
		$txt .= "const SSLKEY_PATH = '../cert/apiclient_key.pem';\n";
		$txt .= "const CURL_PROXY_HOST = '0.0.0.0';\n";
		$txt .= "const CURL_PROXY_PORT = 0;\n";
		$txt .= "const REPORT_LEVENL = 1;\n";
		$txt .= "}\n";
		$txt .= "?>";
		fwrite($myfile, $txt);
		fclose($myfile);		
	}	

	public function read_file($fname)
	{
		$content = '';
		if (!file_exists($fname)) {
		   echo "The file $fname does not exist\n";
		   Yii::$app->end();
		}
		$handle = fopen($fname, "rb");
		while (!feof($handle)) {
			$content .= fread($handle, 10000);
		}
		fclose($handle);
		return $content;
	}
	
	//微信公众号、小程序
	public function saveAvatar($url,$user_id,$entry,$is_resize = 1)
	{
		$defaultPath = Yii::$app->params['miniAvatarPath'].'../default.png';
		$headerPic = Yii::$app->params[$entry.'AvatarPath']."default.png";
		$avatarPath = Yii::$app->params[$entry.'AvatarPath'].$user_id.".jpeg";	

		//头像地址为空，设置默认头像图片
		if(empty($url))
		{
			rename($headerPic,$avatarPath);	
			copy($defaultPath,$headerPic);
			return;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);//
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// 使用自动跳转
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			Yii::error('avatar_Errno'.curl_error($ch));//捕抓异常
			return;
		}
		curl_close($ch);
		
		$len = file_put_contents($avatarPath,$output);
		$arr = getimagesize($avatarPath);
		if($arr[2] == 1)
		{
			imagecreatefromgif($avatarPath);
		}elseif($arr[2] == 3)
		{
			imagecreatefrompng($avatarPath);
		}else
		{
			imagecreatefromjpeg($avatarPath);
		}
		if(file_exists($avatarPath))
		{
			//生成缩略图头像
			$images = new SimpleImageHandle();
			$images->load($avatarPath);
			if($is_resize)
			{
				$images->resize(200,200);				
			}
			$images->save($avatarPath);
		}else{
			rename($headerPic,$avatarPath);	
			copy($defaultPath,$headerPic);
		}
		return;
	}

	//微信网页授权验证 推荐二维码
	public function qrcodeAuthUrl($rurl,$state)
	{
		$rurl = urlencode($rurl);
		return 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Yii::$app->params['appid'].'&redirect_uri='.$rurl.'&response_type=code&scope=snsapi_base&state='.$state.'#wechat_redirect';
	}
	
}

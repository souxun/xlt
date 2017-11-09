<?php 
namespace common\helps;

use Yii;
use common\helps\SimpleImageHandle;
use common\helps\PublicFunction;
use frontend\models\UserBaseInfo;
use frontend\models\TblCity;
use frontend\models\WeixinPostLog;
use frontend\models\WxcodeInfo;
use frontend\models\ScanQrcodeLog;
use frontend\models\RefreeTongji;

//微信调用类
class Weixin {
	private $token;
	private $url;
	private $grant_type;
	private $appid;
	private $secret;
	private $webTokenPath;
	private $timePath;
	public  $webToken;
	public  $expireTime;
	public  $fromUsername;//来源用户openid
	public  $toUsername;//公众平台ID
	public  $MsgType;//消息类型
	public  $content;//文本消息内容
	public  $msgid;//消息ID
	public  $event;//事件subscribe、unsubscribe、click
	public  $eventkey;//event为click时有
	public  $picUrl;//msgtype为image时imageURL
	public  $format;//msgtype为vedio时有，媒体格式
	public  $mediaId;//媒体ID
	public  $location_X;//msgtype=location时纬度坐标
	public  $location_Y;
	public  $scale;//缩放
	public  $Label;//描述

	public function __construct($postStr)
	{
		$this->grant_type="client_credential";//获取access_token填写client_credential
		$this->token=Yii::$app->params['token'];//token
		$this->appid=Yii::$app->params['appid'];//appid
		$this->secret=Yii::$app->params['secret'];//secret
		$this->webTokenPath=Yii::$app->params['webTokenPath'];//webtoken的路径
		$this->timePath=Yii::$app->params['timePath'];//过期时间
 		if (isset($postStr) && $postStr != ""){
 			//Yii::log($postStr,'error');
			$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
			$this->fromUsername = $this->DelNullData($postObj->FromUserName);
			$this->toUsername = $this->DelNullData($postObj->ToUserName);
			$this->MsgType = $this->DelNullData($postObj->MsgType);
			$this->content = $this->DelNullData($postObj->Content);
			$this->msgid = $this->DelNullData($postObj->Msgid);
			$this->event = $this->DelNullData($postObj->Event);
			$this->eventkey = $this->DelNullData($postObj->EventKey);
			$this->picUrl = $this->DelNullData($postObj->PicUrl);
			$this->format = $this->DelNullData($postObj->Format);
			$this->mediaId = $this->DelNullData($postObj->MediaId);
			if ($postObj->Latitude != "" && $postObj->Longitude != "") {
				$this->location_X=$postObj->Latitude;
				$this->location_Y=$postObj->Longitude;
			}else {
				$this->location_X = $this->DelNullData($postObj->Location_X);
				$this->location_Y = $this->DelNullData($postObj->Location_Y);
			}
			$this->scale = $this->DelNullData($postObj->Scale);
			$this->Label = $this->DelNullData($postObj->Label);
		}
	}
	
	
	
	//析构函数
	public function __destruct()
	{
		 
	}
	
	private function DelNullData($data)
	{
		if ($data->isempty) {
			return " ";
		}else {
			return trim($data);
		}
	}
	
	/*
	 * 事件处理方法
	 * 
	 * 
	 */
	public function responseMsg($contentStr="")
	{
		if ($this->MsgType=="text"){//文本消息
			$keyword = $this->content;
		}elseif ($this->MsgType=="event"){
			$keyword = $this->eventkey;
		}else {
			$keyword = "";
		}
		
		if($keyword == 'test'){
			$contentStr = "hello,test\r\n";
			
			echo $this->creat_xml_response($contentStr);
			
		}elseif(($this->event == 'CLICK' && $keyword == 'EXTEND_CODE')){
			$myQrCodePath = Yii::$app->params['myQrCodePath'];
			$userBaseInfo = UserBaseInfo::find()->where('openid=:openid', [":openid"=>$this->fromUsername])->one();
			$user_id = $userBaseInfo->user_id;

			if($userBaseInfo->role_type == 1)
			{			
				//生成二维码 一次生成，终身不变
				$codeCount = count(scandir($myQrCodePath));
				
				if(file_exists($myQrCodePath.$user_id.'.jpg'))
				{
					//unlink($myQrCodePath.$user_id.'.jpg');
				}else{
					//如果推广二维码超过100000个，则自己生成二维码
					if($codeCount >= 99900)
					{
						include_once(__DIR__ ."/PhpQrcode.php");
						$qrcode_url = 'http://'.$_SERVER['HTTP_HOST'].	
						Yii::$app->urlManager->createUrl('/mycode/code-callback');
						$PublicFunction = new PublicFunction();
						$wxauthurl = $PublicFunction->qrcodeAuthUrl($qrcode_url, rand());
						\QRcode::png($wxauthurl,$myQrCodePath.$user_id.'.jpg',0,7);					
					}else{
						//调用微信生成带参数的二维码
						$this->createMyqrcode($user_id);				
					}
				}
				
				$filepath = dirname(__FILE__).'/../../frontend/web/images/extendCode/'.$user_id.'.jpg';
				if(file_exists($filepath))
				{
					
				}else{
					//合并推广二维码图片
					$SimpleImageHandle = new SimpleImageHandle;
					$SimpleImageHandle->mergerImg(array(
						'user_id'=>$user_id,
						'nickname'=>"微信名：".$userBaseInfo->nickname,
						'avatar'=>dirname(__FILE__).'/../../frontend/web/images/miniAvatars/'.$user_id.'.jpeg',
						'code'=>dirname(__FILE__).'/../../frontend/web/images/myqrcode/'.$user_id.'.jpg',
						'dst'=>dirname(__FILE__).'/../../frontend/web/images/extend_dst.jpg',
					));					
				}
				
				$WxcodeInfo = WxcodeInfo::find()->select(['id'])->where("user_id=:user_id",[":user_id"=>$user_id])->one();
				if(empty($WxcodeInfo))
				{
					//上传临时素材(推广二维码图片)
					$media_id = $this->uploadTempFile('image',$filepath);
					
					$WxcodeInfo = new WxcodeInfo();
					$WxcodeInfo->user_id = $user_id;
					$WxcodeInfo->media_id = $media_id;
					$WxcodeInfo->expire_time = date("Y-m-d H:i:s",strtotime("+2 days"));
					$WxcodeInfo->save();
				}else{
					$now = date("Y-m-d H:i:s");
					if(strtotime($WxcodeInfo->expire_time) < strtotime($now)){
						//上传临时素材(推广二维码图片)
						$media_id = $this->uploadTempFile('image',$filepath);
						
						$WxcodeInfo->media_id = $media_id;
						$WxcodeInfo->expire_time = date("Y-m-d H:i:s",strtotime("+2 days"));
						$WxcodeInfo->save();						
					}
				}

				//发送二维码图片
				echo $this->creat_xml_img_response($WxcodeInfo->media_id);
				
			}else{
				$text = "雪莲贴邀请您成为推广大使，想让您的手机变成印钞机吗？一万个想法不如一次马上行动！点击马上成为推广大使。";
				$json = '{
					"touser":"'.$this->fromUsername.'",
					"msgtype":"text",
					"text":
					{
						"content":"<a href=\"https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Yii::$app->params['appid'].'&redirect_uri='.Yii::$app->params['appUrl'].'/weixin/wx-client&response_type=code&scope=snsapi_base&state=1000#wechat_redirect\">'.$text.'</a>"
					}
				}';
				$this->textmsg($json);
			}	
		}elseif($this->event == 'unsubscribe'){
			//取消关注
		}else{
			echo $this->create_cust_msg();
		}	
	}
	
	/**
	 * 保存post 日志
	 */
	public  function SavePost()
	{
		$model = new WeixinPostLog;
		$model->fromuser = $this->fromUsername;
		$model->msgtype = $this->MsgType;
		$model->event = $this->event or "0";
		$model->eventkey = $this->eventkey or "0";
		$model->content = $this->content or "0";
		$model->picUrl = $this->picUrl or "0";
		$model->format = $this->format or "0";
		$model->mediaId = $this->mediaId or "0";
		$model->location_x = $this->location_X or "0";
		$model->location_y = $this->location_Y or "0";
		$model->scale = $this->scale or "0";
		$model->label = $this->Label or "0";
		$model->create_time = date('Y-m-d H:i:s');
		//Yii::log($model->event,'error');
		if($this->event == "subscribe"){
			if ($model->save()){
				$return = $this->CreateUserInfo($this->fromUsername);
				
				$title = '尊敬的'.$return['nickname'].',欢迎关注王道系统！';
				$desc = "王道成就2亿微商，我们是认真的。";
				$purl = Yii::$app->params['appUrl'].'/images/guanzu.jpg?2';
				$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$this->appid."&redirect_uri=".Yii::$app->params['appUrl']."/weixin/wx-client&response_type=code&scope=snsapi_base&state=1#wechat_redirect";
				echo $this->creat_news_response($title,$desc,$purl,$url);
			}
			return ;
		}
		$contentStr = "";
		if($this->MsgType == "event" && $this->event=="LOCATION"){
			$model->save();
			return;
		}elseif($this->MsgType == "event" && $this->event == 'SCAN'){
			//扫描带参数二维码事件
			$model->save();
			//保存扫码记录
			$this->saveScanLog($this->fromUsername,$this->eventkey);
			return;
		}
		if ($model->save())
		{ 
			$this->responseMsg($contentStr);
		}else {
			return false;
		}
	}
	
	public function unicode2utf8_2($str)
	{	
		//关于unicode编码转化的第二个函数，用于显示emoji表情
        $str = '{"result_str":"'.$str.'"}';	//组合成json格式
		$strarray = json_decode($str,true);	//json转换为数组，利用 JSON 对 \uXXXX 的支持来把转义符恢复为 Unicode 字符（by 梁海）
		return $strarray['result_str'];
    }
	
	//验证token签名，微信认证使用
	
	private function checkSignature($signature,$timestamp,$nonce)
	{
	
		$token = $this->token;
		$tmpArr = [$token, $timestamp, $nonce];
		sort($tmpArr,SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
	
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

	//验证消息来源真实性
	public function validGet($signature,$timestamp,$nonce)
	{
		if ($this->checkSignature($signature,$timestamp,$nonce)){
			return true;
		}else {
			return false;
		}
	}
	
	/**
	 * 网址接入验证方法
	 * @signature — 微信加密签名
	 * @timestamp — 时间戳
	 * @nonce — 随机数
	 * @echostr — 随机字符串
	 * @return string
	 */
	public function valid($echoStr,$signature,$timestamp,$nonce)
	{
		//valid signature , option
		if ($this->checkSignature($signature,$timestamp,$nonce)) {
			return $echoStr;
		}
	}
	
	
	public function load_event()
	{
		return $this->event;
	}
	
	public function load_keyword()
	{
		return $this->content;
	}
	 
	/**
	 * 关注用户自动生成基本信息
	 */
    private function CreateUserInfo($openid)
    {
		$userinfo = $this->getUserInfo($openid);
    	$uinfo = UserBaseInfo::find()->select(['id','user_id'])->where('openid=:openid',[':openid'=>$userinfo->openid])->one();
		
		$nickname = $userinfo->nickname;
		//可能包含二进制emoji表情字符串  暴露出unicode
		$tmpNickname = json_encode($nickname);
		//将emoji的unicode替换为空，其它保留
		$tmpNickname = preg_replace("#(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})#i","",$tmpNickname);
		$nickname = json_decode($tmpNickname);

		$TblCity = new TblCity;
		$city = $TblCity->findCity($userinfo->city);
		$province = $TblCity->findCity($userinfo->province);
		$city = $city['id'];
		$province = $province['id'];
		$sex = $userinfo->sex;
		$PublicFunction = new PublicFunction;
		if(empty($uinfo))
		{
	        $uinfo = new UserBaseInfo();
			//user_id为最后一个id+1
			$lastInfo = UserBaseInfo::find()->select('user_id')->orderby('id desc')->one();			
			$lastUserId = $lastInfo->user_id;
	        $uinfo->user_id = (string)($lastUserId+1);
	        //$uinfo->unionid = $userinfo->unionid;
			//新用户就生成session_id
	        //$uinfo->session_id = $PublicFunction->getSessionId($uinfo->user_id);
    	}
		$uinfo->openid = $openid;
		$uinfo->nickname = $nickname;   
		$uinfo->sex = $sex;
		$uinfo->province = (string)$province;
		$uinfo->city = (string)$city;
		if($uinfo->save())
		{								
			$user_id = $uinfo->user_id;
			
			if(!empty($this->eventkey))
			{
				//扫描带参数二维码关注  记录扫码日志
				$temp_arr = explode('_',$this->eventkey);
				$this->saveScanLog($this->fromUsername,$temp_arr[1]);
			}			
			//个人信息保存成功   处理头像				
			$url = isset($userinfo->headimgurl) ? $userinfo->headimgurl : '';
			$PublicFunction->saveAvatar($url,$user_id,'mini');
			
			return ['user_id'=>$user_id,'nickname'=>$nickname];
		}
   }
   
	/*
	* 保存扫码日志
	* 1) eventkey一定要是会员用户;
	* 2) 未付费;
	* 3) 上一次扫码记录已经超过24小时;
	* 满足以上三个条件，则记录扫码日志并确定继承关系
	* 在付费的时候如果还没有继承关系则归属公司
	*/
	public function saveScanLog($openid,$eventkey)
	{
		$yxInfo = UserBaseInfo::find()->select(['openid','role_type','a_from','nickname','wx','phone'])->where("user_id=:user_id",[":user_id"=>$eventkey])->one();
		if(empty($yxInfo) || $yxInfo->role_type == 0)
		{
			return;
		}
		
		$userInfo = UserBaseInfo::find()->select(['id','nickname','role_type','a_from','b_from'])->where("openid=:openid",[":openid"=>$openid])->one();
		if($userInfo->role_type == 1)
		{
			//已付费
			return;
		}	

		$scanFlag = true;
		$last = ScanQrcodeLog::find()->where("openid=:openid and qrcode_type='recommend'",[":openid"=>$openid])->orderby("create_time desc")->one();
		if(!empty($last))
		{
			$lastTime = $last->create_time;
			$tmpTime = date("Y-m-d H:i:s",strtotime("$lastTime +24 hours"));
			$now = date("Y-m-d H:i:s");
			if(strtotime($tmpTime) >= strtotime($now))
			{
				$scanFlag = false;
			}
		}
		
		if($scanFlag)
		{
			$ScanQrcodeLog = new ScanQrcodeLog();
			$ScanQrcodeLog->openid = $openid;
			$ScanQrcodeLog->yx_from = $eventkey;
			$ScanQrcodeLog->save();	
			
			//老推荐人
			$old_a_from = $userInfo->a_from;
			$old_b_from = $userInfo->b_from;
						
			//扫码有效  直接确定继承关系
			$userInfo->a_from = $eventkey;
			$userInfo->b_from = $yxInfo->a_from;				
			$userInfo->save(false,['a_from','b_from']);
			
			//如果之前已经有继承关系且现在的关系跟以前不一样，
			//要重新计算老推荐人统计表的值
			if(!empty($old_a_from) && $old_a_from != $userInfo->a_from)
			{
				$this->refreeTongjiSubUpdate($old_a_from);
				if(!empty($old_b_from))
					$this->refreeTongjiSubUpdate($old_b_from);
			}
			
			//更新新推荐人统计表的值
			$this->refreeTongjiSubUpdate($userInfo->a_from);
			if(!empty($userInfo->b_from))
				$this->refreeTongjiSubUpdate($userInfo->b_from);

			//扫码有效，给扫码人推客服消息			
			$contentStr = "恭喜您成功关注雪莲贴\n";
			if(empty($yxInfo->name))
			{
				//真实姓名为空显示昵称
				$contentStr .= "您的推荐人是【".$yxInfo->nickname."】\n";
			}else{
				$contentStr .= "您的推荐人是【".$yxInfo->nickname."】,姓名:".$yxInfo->name.",手机号：".$yxInfo->phone."\n";
			}
				
			echo $this->creat_xml_response($contentStr);
			
			//给推荐人推消息
			$json1 = '{
				"touser":"'.$yxInfo->openid.'",
				"msgtype":"text",
				"text":
				{
					"content":"恭喜您，【'.$userInfo->nickname.'】通过扫描您的“推广二维码”关注雪莲贴，成为您的准1级营销总监，请赶紧跟进成交。特别提醒：关系锁定时间为24小时，24小时后未付款自动解除关系，再扫描其他人二维码成交将会计入别人的收益。"
				}
			}';
			$this->textmsg($json1);
			
			if(!empty($yxInfo->a_from) && $yxInfo->a_from != Yii::$app->params['companyId'])
			{
				$twoInfo = UserBaseInfo::find()->select(['openid'])->where("user_id=:user_id",[":user_id"=>$yxInfo->a_from])->one();
				$json2 = '{
					"touser":"'.$twoInfo->openid.'",
					"msgtype":"text",
					"text":
					{
						"content":"恭喜您，【'.$userInfo->nickname.'】通过扫描您的1级营销总监【'.$yxInfo->nickname.'】关注雪莲贴，成为您的准2级业务主管，请赶紧跟进成交。特别提醒：关系锁定时间为24小时，24小时后未付款自动解除关系，再扫描其他人二维码成交将会计入别人的收益。"
					}
				}';
				$this->textmsg($json2);	
			}
		}		
	}	
	
	//推广统计表数据插入或更新
	public function refreeTongjiSubUpdate($yx_from)
	{
		$tongji = RefreeTongji::find()->select(['id'])->where("user_id=:user_id",[":user_id"=>$yx_from])->one();
		if(empty($tongji)){
			$tongji = new RefreeTongji();
			$tongji->user_id = $yx_from;
		}
		$subNum = UserBaseInfo::find()->where("a_from=:user_id or b_from=:user_id",[":user_id"=>$yx_from])->count();
		$tongji->subNum = $subNum;

        $payNum = UserBaseInfo::find()->where("role_type=1 and (a_from=:user_id or b_from=:user_id )",[":user_id"=>$yx_from])->count();
        $tongji->payNum = $payNum;

		$tongji->save(false);		
	}   

	//同步微信资料
	public function syncWeixinInfo($openid)
	{
		$returnMsg = '您好！您的微信资料同步成功！';
		$userinfo = $this->getUserInfo($openid);
		if (isset($userinfo->subscribe) && 1 == $userinfo->subscribe) 
		{
			//只有用户已经关注了平台才可能获取到头像、昵称等信息
	    	$nickname = $userinfo->nickname;
			//可能包含二进制emoji表情字符串  暴露出unicode
			$tmpNickname = json_encode($nickname);
			//将emoji的unicode替换为空，其它保留
			$tmpNickname = preg_replace("#(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})#i","",$tmpNickname);
			$nickname = json_decode($tmpNickname);			
			
			$TblCity = new TblCity;
			$city = $TblCity->findCity($userinfo->city);
			$province = $TblCity->findCity($userinfo->province);
			$city = $city['id'];
			$province = $province['id'];
			$sex = $userinfo->sex;
			$usermodel = UserBaseInfo::find()->select(['id','user_id'])->where("openid=:openid",[":openid"=>$openid]);
			$usermodel->nickname = $nickname;
			$usermodel->sex = $sex;
			$usermodel->province = (string)$province;
			$usermodel->city = (string)$city;
			
			if($usermodel->save())
			{
				$user_id = $usermodel->user_id;
				//个人信息保存成功   同步头像
				$PublicFunction = new PublicFunction;
				$url = isset($userinfo->headimgurl) ? $userinfo->headimgurl : '';
				$PublicFunction->saveAvatar($url,$user_id,'mini');
			}else
				$returnMsg = '对不起，您的微信资料同步失败！请重新同步。';					
		}else
			$returnMsg = '对不起，您尚未关注雪莲贴，无法同步微信资料！';
		return $returnMsg;
	}
	
	
	/**
	 * 创建XML格式的response
	 * @fromUsername - 消息发送方微信号
	 * @toUsername - 消息接收方微信号
	 * @contentStr - 需要发送的文本内容
	 * @return xml
	 */
	public function creat_xml_response($contentStr)
	{
		$msgType = "text";
		$time = time();
		$textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <FuncFlag>0</FuncFlag>
                            </xml>";
		$resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $time, $msgType, $contentStr);
		return $resultStr;
	}
	
	/**
	 * 创建XML格式的response
	 * @fromUsername - 消息发送方微信号
	 * @toUsername - 消息接收方微信号
	 * @media_id - 需要发送的多媒体素材id
	 * @return xml
	 */
	public function creat_xml_img_response($media_id)
	{
		$msgType = "image";
		$time = time();
		$textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
							<Image>
                            <MediaId><![CDATA[%s]]></MediaId>
                            </Image>
                            </xml>";
		$resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $time, $msgType, $media_id);
		return $resultStr;
	}
	
	
	public function create_cust_msg(){//客服功能
		$time = time();
		$reply="
		<xml>
		<ToUserName><![CDATA[%s]]></ToUserName>
		<FromUserName><![CDATA[%s]]></FromUserName>
		<CreateTime>%s</CreateTime>
		<MsgType><![CDATA[transfer_customer_service]]></MsgType>
		</xml>";
		$reply_str=sprintf($reply,$this->fromUsername,$this->toUsername,$time);
		return $reply_str;
	}
	
	
	private function encrypt($str)
	{
		return md5($str);
	}
	
	//组合欢迎消息，本篇为1个图文消息
	public function creat_news_response($title,$desc,$purl,$url)
	{
		$time = time();
		$ArticleCount = "1";
		$title1=$title;
		$desc1=$desc;
		$purl1=$purl;
		$url1=$url;
		$textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>1</ArticleCount>
                            <Articles>
							<item>
 							<Title><![CDATA[%s]]></Title>
 							<Description><![CDATA[%s]]></Description>
							<PicUrl><![CDATA[%s]]></PicUrl>
 							<Url><![CDATA[%s]]></Url>
 							</item>
                            </Articles>
                            </xml>";
		$resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $time,$title1,$desc1,$purl1,$url1);
		return $resultStr;
	}
	//组合图文列表消息，本篇为N个图文消息
	public function creat_mutil_news_response($arr_list)
	{
		$time = time();
		$ArticleCount = count($arr_list);
		$textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[news]]></MsgType>
							<ArticleCount>%s</ArticleCount>
                            <Articles>";
 							
		$siglenewsTpl="<item><Title><![CDATA[%s]]></Title>
 							<Description><![CDATA[%s]]></Description>
							<PicUrl><![CDATA[%s]]></PicUrl>
 							<Url><![CDATA[%s]]></Url></item>";
		$mutilNewsText="";
		foreach ($arr_list as $k=>$v){
			$news=explode(';', $v);
			if (count($news)=='4'){//长度一定是4 否则会出错
				$mutilNewsText.=sprintf($siglenewsTpl,$news['0'],$news['1'],$news['2'],$news['3']);
			}
		}
		$resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $time,$ArticleCount);
		$resultStr.=$mutilNewsText;
		$resultStr.="</Articles>
                            </xml>";
		return $resultStr;
	}	
	
	/*
	 * 发送微信模板消息curl
	 */
	public function wtw_request($url,$data=null){
		$curl = curl_init(); // 启动一个CURL会话
		curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
		//curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
		if($data != null){
			curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
		}
		curl_setopt($curl, CURLOPT_TIMEOUT, 300); // 设置超时限制防止死循环
		curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$info = curl_exec($curl); // 执行操作
		if (curl_errno($curl)) {
			Yii::log('Errno:'.curl_getinfo($curl),'error');//捕抓异常
			//var_dump(curl_getinfo($curl));
		}
		return $info;
	}	
	
	public function getAccess(){
		$param = ["appid"=>$this->appid,"secret"=>$this->secret,"grant_type"=>$this->grant_type];
		$get_args="";
		foreach ($param as $k=>$v){
			$get_args .= $k."=".$v."&";
		}
		$ass_url="https://api.weixin.qq.com/cgi-bin/token?";
		$ass_url .= $get_args;
		$PublicFunction = new PublicFunction;
		$output = $PublicFunction->getClient($ass_url);
		$expire_time = time()+7000;
		//写入token文件
		file_put_contents($this->webTokenPath, $output->access_token);
		//写入过期时间
		file_put_contents($this->timePath, $expire_time);
		return $output;
	}
	
	public function haveAccessToken(){
		$this->webToken=file_get_contents($this->webTokenPath);
		$this->expireTime=file_get_contents($this->timePath);
		//if ($this->webToken && $this->expireTime && $this->expireTime+100 > time()){//token存在并且没有过期
		if ($this->webToken && $this->expireTime && $this->expireTime >= time()){//token存在并且没有过期
			return true;
		}else {
			$this->getAccess();
			$this->webToken=file_get_contents($this->webTokenPath);
			$this->expireTime=file_get_contents($this->timePath);
			return true;
		}
	}

	
	public function getUserInfo($openid){
		$this->haveAccessToken();
		$userUrl = "https://api.weixin.qq.com/cgi-bin/user/info?";
		$param = ['access_token'=>$this->webToken,'openid'=>$openid];
		$get_args="";
		foreach ($param as $k=>$v){
			$get_args .= $k."=".$v."&";
		}
		$userUrl .=$get_args;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $userUrl);//
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// 使用自动跳转
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			Yii::error('Errno'.curl_error($ch));//捕抓异常
			return;
		}
		curl_close($ch);
				
		$output = json_decode($output);
		return $output;
	}
	
	/*
	 * 发送模板消息
	 */
	public function templetemsg($msg_json)
	{
		$this->haveAccessToken();//刷新生成token
		$ACCESS_TOKEN = $this->webToken;
		$msg_url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$ACCESS_TOKEN."";		
	   
		$result = $this->wtw_request($msg_url,$msg_json);
		return $result;
	}

	/*
	 * 发送文字消息
	 */
	public function textmsg($msg_json)
	{
		$this->haveAccessToken();//刷新生成token
		$ACCESS_TOKEN = $this->webToken;
		$msg_url = "https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=".$ACCESS_TOKEN."";
	
		$result = $this->wtw_request($msg_url,$msg_json);
		return $result;
	}
	
	//创建带参数二维码并存放
	public function createMyqrcode($yx_from = 0)
	{
		$this->haveAccessToken();
		$post_data = '{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$yx_from.'"}}}';
		$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$this->webToken;
		
		$PublicFunction = new PublicFunction;
		$result = $PublicFunction->postClient($url,$post_data);	
		$ticket = $result->ticket;
		
		$getcodeUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
		
		$ch = curl_init($getcodeUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);//
		curl_setopt($ch, CURLOPT_NOBODY, 0);// 只取body头
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$output = curl_exec($ch);
		$httpinfo = curl_getinfo($ch);
		if (curl_errno($ch)) {
			Yii::error('create_qrcode_errno:'.curl_error($ch),'error');
			//echo 'Errno'.curl_error($ch);//捕抓异常
			return;
		}
		curl_close($ch);
		
		$codePath = Yii::$app->params['myQrCodePath'].$yx_from.".jpg";
		$len = file_put_contents($codePath,$output);
		
		$arr = getimagesize($codePath);
		if($arr[2] == 1)
		{
			$code_img = imagecreatefromgif($codePath);
		}elseif($arr[2] == 3)
		{
			$code_img = imagecreatefrompng($codePath);
		}else
		{
			$code_img = imagecreatefromjpeg($codePath);
		}
		return $codePath;
	}

	//上传临时素材
	public function uploadTempFile($type = 'image',$filepath)
	{
		//$filedata = array("media"=>"@".$filepath);
		//php>=5.6时要采用下面的写法
		$filedata = [new \CurlFile($filepath)];
		$this->haveAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->webToken."&type=".$type;
		$PublicFunction = new PublicFunction;
		$result = $PublicFunction->postClient($url,$filedata);
		if(isset($result->media_id))
		{
			return $result->media_id;
		}
		return 'error';
	}	
	
	   //同步微信资料 for 接口
    public function syncWeixinInfo2($openid)
    {
        $returnMsg = true;
        $userinfo = $this->getUserInfo($openid);
        if (isset($userinfo->subscribe) && 1 == $userinfo->subscribe)
        {
            //只有用户已经关注了平台才可能获取到头像、昵称等信息
            $nickname = $userinfo->nickname;
            //可能包含二进制emoji表情字符串  暴露出unicode
            $tmpNickname = json_encode($nickname);
            //将emoji的unicode替换为空，其它保留
            $tmpNickname = preg_replace("#(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})#i","",$tmpNickname);
            $nickname = json_decode($tmpNickname);

            $TblCity = new TblCity;
            $city = $TblCity->findCity($userinfo->city);
            $province = $TblCity->findCity($userinfo->province);
            $city = $city['id'];
            $province = $province['id'];
            $sex = $userinfo->sex;
            $usermodel = UserBaseInfo::find()->select(['id','user_id'])->where("openid=:openid",[":openid"=>$openid])->one();
            $usermodel->nickname = $nickname;
            $usermodel->sex = $sex;
            $usermodel->province = (string)$province;
            $usermodel->city = (string)$city;

            if($usermodel->save())
            {
                $user_id = $usermodel->user_id;
                //个人信息保存成功   同步头像
                $PublicFunction = new PublicFunction;
                $url = isset($userinfo->headimgurl) ? $userinfo->headimgurl : '';
                $PublicFunction->saveAvatar($url,$user_id,'mini');
            }else
                $returnMsg = '对不起，您的微信资料同步失败！请重新同步。';
        }else
            $returnMsg = '对不起，您尚未关注雪莲贴，无法同步微信资料！';
        return $returnMsg;
    }
}





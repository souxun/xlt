<?php 
//微信调用类
class weixin {
	private $token;
	private $url;
	private $grant_type;
	private $appid;
	private $secret;
	private $webTokenPath;
	private $timePath;
	private $webToken;
	private $expireTime;
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
		$this->token=Yii::app()->params['token'];//token
		$this->appid=Yii::app()->params['appid'];//appid
		$this->secret=Yii::app()->params['secret'];//secret
		$this->webTokenPath=Yii::app()->params['webTokenPath'];//webtoken的路径
		$this->timePath=Yii::app()->params['timePath'];//过期时间
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
		
		if($keyword == '人数'){
			//统计每天及总计的书、赞助商  黄总、孙总、赵总、余水映
			$userinfo = UserBaseInfo::model()->findByAttributes(array('openid'=>$this->fromUsername));
			if(is_object($userinfo) && in_array($userinfo->unionid,Yii::app()->params['noticeTemplateUser']))
			{
				$connect = Yii::app()->db;
				$today_command = $connect->createCommand("select count(user_id) as today_sum from people_user_base_info where to_days(create_time)=to_days(now())");
				$today_result = $today_command->queryAll();
				$today_arr = array("total"=>0,"book_pay"=>0,"sponsor_pay"=>0);
				foreach($today_result as $v)
				{
					$today_arr["total"] += $v['today_sum'];
				}
				//今天卖书总计
				$todayBookInfo = WeixinPay::model()->find(array("select"=>"sum(attach) as attach","condition"=>"pay_type=1 and TO_DAYS(timestamp)=TO_DAYS(now())","group"=>"pay_type"));
				$today_arr["book_pay"] = empty($todayBookInfo->attach) ? 0 : $todayBookInfo->attach;
				//今天新增赞助商
				$today_arr["sponsor_pay"] = WeixinPay::model()->count("pay_type=2 and TO_DAYS(timestamp)=TO_DAYS(now())");
				
				//总人数
				$total_command = $connect->createCommand("select role_type,count(user_id) as total_sum from people_user_base_info where user_id not in('100000') group by role_type");
				$total_result = $total_command->queryAll();
				$total_arr = array("total"=>0,"book_pay"=>0,"sponsor_pay"=>0);
				foreach($total_result as $v)
				{
					$total_arr["total"] += $v['total_sum'];   
					if($v['role_type'] == 2 || $v['role_type'] == 3) $total_arr["sponsor_pay"] += $v['total_sum'];
				}
				//30原始数据不算
				$total_arr['total'] -=  30;
				//卖书总数量
				$totalBookInfo = WeixinPay::model()->find(array("select"=>"sum(attach) as attach","condition"=>"pay_type=1","group"=>"pay_type"));
				$total_arr['book_pay'] = empty($totalBookInfo->attach) ? 0 : $totalBookInfo->attach;

				//组织回复内容
				$contentStr = "今天新增人数:".$today_arr['total']."\r\n";
				$contentStr .= "今天新增卖书:".$today_arr['book_pay']."\r\n";
				$contentStr .= "今天新增赞助商:".$today_arr['sponsor_pay']."\r\n";
				$contentStr .= "总人数:".$total_arr['total']."\r\n";
				$contentStr .= "卖书总数量:".$total_arr['book_pay']."\r\n";	
				$contentStr .= "赞助商总人数:".$total_arr['sponsor_pay'];	
				echo $this->creat_xml_response($contentStr);			
			}
		}elseif($keyword == '红包'){
			$userinfo = UserBaseInfo::model()->findByAttributes(array('openid'=>$this->fromUsername));
			if(is_object($userinfo) && in_array($userinfo->unionid,Yii::app()->params['noticeTemplateUser']))
			{
				//查询红包总数
				$connect = Yii::app()->db;
				$commandTongji = $connect->createCommand("SELECT count(id) as total,sum(money) as money,sum(case when is_give=1 then money end) as give_money from `people_hongbao_record` where type=0");
				$hongbaoTongji = $commandTongji->queryRow();
				$contentStr = "到目前为止，总共领取红包".$hongbaoTongji['total']."个，总金额".$hongbaoTongji['money']."元，已结算金额".$hongbaoTongji['give_money']."元。";
				
				$commandYester = $connect->createCommand("SELECT count(id) as total,sum(money) as money from `people_hongbao_record` where create_time>='".date("Y-m-d 00:00:00",strtotime("-1 days"))."' and create_time<='".date("Y-m-d 23:59:59",strtotime("-1 days"))."' and type=0"); 
				$hongbaoYester = $commandYester->queryRow();
				$contentStr .= date("Y年m月d日",strtotime("-1 days"))."，发放红包".$hongbaoYester['total']."个，金额".$hongbaoYester['money']."元";
				
				echo $this->creat_xml_response($contentStr);
			}
		}elseif($keyword == '大姨妈'){
			$userinfo = UserBaseInfo::model()->findByAttributes(array('openid'=>$this->fromUsername));
			if(is_object($userinfo) && (in_array($userinfo->unionid,Yii::app()->params['noticeTemplateUser']) || $userinfo->user_id == '449052'))
			{
				$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Yii::app()->params['appid'].'&redirect_uri='.Yii::app()->params['wxUrl'].'/C/dym.html&response_type=code&scope=snsapi_base&state=1000#wechat_redirect';
				
				$contentStr = '<a href="'.$url.'">点击查看大姨妈中奖记录</a>';
				echo $this->creat_xml_response($contentStr);
			}
		}elseif($keyword == '竞宝'){
			$userinfo = UserBaseInfo::model()->findByAttributes(array('openid'=>$this->fromUsername));
			if(is_object($userinfo) && in_array($userinfo->unionid,Yii::app()->params['noticeTemplateUser']))
			{
				$luck_id = 1;
				$luckInfo = LuckInfo::model()->findByPk($luck_id);
				//查询当天参与人次  及总共参与人次
				$todayJoinLuckInfo = LuckJoinRecord::model()->find(array("select"=>"sum(join_num) as join_num","condition"=>"luck_id=:luck_id and status=1 and TO_DAYS(complete_time)=TO_DAYS(now())","params"=>array(":luck_id"=>$luck_id)));
				$todayLuckNum = $todayJoinLuckInfo->join_num+0;
				$totalJoinLuckInfo = LuckJoinRecord::model()->find(array("select"=>"sum(join_num) as join_num","condition"=>"luck_id=:luck_id and status=1","params"=>array(":luck_id"=>$luck_id)));
				$totalLuckNum = $totalJoinLuckInfo->join_num+0;
				
				$contentStr = "到目前为止，".$luckInfo->name."的1元竞宝参与人次已达到".$totalLuckNum."，今日参与人次为".$todayLuckNum."人。";
				
				echo $this->creat_xml_response($contentStr);
			}
		}elseif(($this->event == 'CLICK' && $keyword == 'EXTEND_CODE')){
			$myQrCodePath = Yii::app()->params['myQrCodePath'];
			$userBaseInfo = UserBaseInfo::model()->find('openid=:openid', array(":openid"=>$this->fromUsername));
			$user_id = $userBaseInfo->user_id;

			if($userBaseInfo->wx_off == 1)
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
						include 'PhpQrcode.php';
						$qrcode_url = 'http://'.$_SERVER['HTTP_HOST'].Yii::app()->createUrl("myCode/codeCallback",array("yx_from"=>$user_id));
						$u = new MainUse();
						$wxauthurl = $u->qrcodeAuthUrl($qrcode_url, rand());
						QRcode::png($wxauthurl,$myQrCodePath.$user_id.'.jpg',0,7);					
					}else{
						//调用微信生成带参数的二维码
						$this->createMyqrcode($user_id);					
					}
				}
				
				$filepath = dirname(__FILE__).'/../../images/extendCode/'.$user_id.'.jpg';
				if(file_exists($filepath))
				{
					
				}else{
					//合并推广二维码图片
					$SimpleImageHandle = new SimpleImageHandle;
					$SimpleImageHandle->mergerImg(array(
						'user_id'=>$user_id,
						'nickname'=>'['.$userBaseInfo->nickname.']',
						'avatar'=>dirname(__FILE__).'/../../images/smallAvatars/'.$user_id.'.jpeg',
						'code'=>dirname(__FILE__).'/../../images/myqrcode/'.$user_id.'.jpg',
						'dst'=>dirname(__FILE__).'/../../images/extend_dst.jpg',
					));					
				}
				
				$WxcodeInfo = WxcodeInfo::model()->find("user_id=:user_id",array(":user_id"=>$user_id));
				if(empty($WxcodeInfo)){
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
				$text = "天枭梦想汇邀请您成为推广大使，想让您的手机变成印钞机吗？一万个想法不如一次马上行动！点击马上成为推广大使。";
				$json = '{
					"touser":"'.$this->fromUsername.'",
					"msgtype":"text",
					"text":
					{
						"content":"<a href=\"https://open.weixin.qq.com/connect/oauth2/authorize?appid='.Yii::app()->params['appid'].'&redirect_uri='.Yii::app()->params['wxUrl'].'/C/index&response_type=code&scope=snsapi_base&state=1000#wechat_redirect\">'.$text.'</a>"
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
		//$arr_post=array();
		$model->fromuser =$this->fromUsername;
		$model->msgtype=$this->MsgType;
		$model->event=$this->event or "0";
		$model->eventkey=$this->eventkey or "0";
		$model->content=$this->content or "0";
		$model->picUrl=$this->picUrl or "0";
		$model->format=$this->format or "0";
		$model->mediaId=$this->mediaId or "0";
		$model->location_x=$this->location_X or "0";
		$model->location_y=$this->location_Y or "0";
		$model->scale=$this->scale or "0";
		$model->label=$this->Label or "0";
		$model->create_time = date('Y-m-d H:i:s');
		//Yii::log($model->event,'error');
		if($this->event=="subscribe"){
			//echo $this->creat_xml_response('天枭梦想汇功能还未完善，敬请期待。');
			//Yii::app()->end();
			if ($model->save()){
				$userid=$this->CreateUserInfo($this->fromUsername);
				//echo $this->creat_img_welcome_response($this->fromUsername, $userid);
				$contentStr = "恭喜您成功关注梦想汇\n";
				$contentStr .= "您想把手机变成印钞机吗？\n";
				$contentStr .= "您想成为微信营销高手月入数万吗？\n";	
				$contentStr .= "您想拥有百万高端客户瞬间实现财富核裂变吗？\n";	
				$contentStr .= "恭喜您来对了！\n";	
				$contentStr .= "赶紧进入平台，为您揭开财富核裂变的秘密。\n";	
				$contentStr .= "需要进群接受进一步辅导吗？请联系叶子老师：135-5268-0107，微信号：W1328670913；美丽老师：15049564792（手机和微信号）";	
				echo $this->creat_xml_response($contentStr);
				
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
	
	
	
	
	//验证token签名，微信认证使用
	
	private function checkSignature($signature,$timestamp,$nonce)
	{
	
		$token = $this->token;
		$tmpArr = array($token, $timestamp, $nonce);
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
	 * 
	 * 
	 * 
	 */
    private function CreateUserInfo($openid)
    {		
    	$uinfo = UserBaseInfo::model()->findByAttributes(array('openid'=>$openid));
    	if (is_object($uinfo)){
			//已经有用户资料，直接返回user_id
			if(!empty($this->eventkey))
			{
				//扫描带参数二维码关注  记录扫码日志
				$temp_arr = explode('_',$this->eventkey);
				$this->saveScanLog($this->fromUsername,$temp_arr[1]);
			}
    		return $uinfo->user_id;
    	}else{
			$userinfo = $this->getUserInfo($openid);
	    	$nickname = $userinfo->nickname;
			//可能包含二进制emoji表情字符串  暴露出unicode
			$tmpNickname = json_encode($nickname);
			//将emoji的unicode替换为空，其它保留
			$tmpNickname = preg_replace("#(\\\ud[0-9a-f]{3})|(\\\ue[0-9a-f]{3})#i","",$tmpNickname);
			$nickname = json_decode($tmpNickname);

			if($userinfo->country == '中国'){
				$city = TblCity::model()->findCity($userinfo->city);
				$province = TblCity::model()->findCity($userinfo->province);				
				$city = $city['id'];
				$province = $province['id'];
			}else{
				$province = TblCity::model()->findCity($userinfo->country);				
				$city = '';
				$province = $province['id'];
			}
	        $sex = $userinfo->sex;
	        $usermodel = new UserBaseInfo();
			//user_id为最后一个id+1
			$lastUser = UserBaseInfo::model()->find(array("condition"=>"user_id<>100000","order"=>"id desc"));
	        $usermodel->user_id = is_object($lastUser) ? ($lastUser->user_id+1) : 100011;
	        $usermodel->openid = $openid;
	        $usermodel->unionid = $userinfo->unionid;
	        $usermodel->nickname = filterSql($nickname);   //过滤特殊字符
	        $usermodel->sex = $sex;
	        $usermodel->province = $province;
	        $usermodel->city = $city;
			$usermodel->wx_off = 0;   //专属二维码默认关闭，等到购买书后才有
			
	        if($usermodel->save())
	        {
				$usermodel->bsort = $usermodel->id;    //默认排序跟id值一样
				$usermodel->save();
				
				if(!empty($this->eventkey))
				{
					//扫描带参数二维码关注  记录扫码日志
					$temp_arr = explode('_',$this->eventkey);
					$this->saveScanLog($this->fromUsername,$temp_arr[1]);
				}
								
				$user_id = $usermodel->user_id;
				//个人信息保存成功   处理头像
				//Yii::log('headimgUrl:'.$userinfo->headimgurl,'error');
				
				if (!isset($userinfo->headimgurl)){
					$headerPic = Yii::app()->params['avatarPath']."default.jpeg";
					rename($headerPic, Yii::app()->params['avatarPath'].$user_id.".jpeg");	
					copy(Yii::app()->params['avatarPath'].'../default.jpeg',$headerPic);
				}else{
					$avartar = $this->getAvatar($userinfo->headimgurl, $userinfo->openid,$user_id);
					$headerPic = Yii::app()->params['avatarPath'].$userinfo->openid.".jpeg";
					if(file_exists($headerPic))
					{
						rename($headerPic, Yii::app()->params['avatarPath'].$user_id.".jpeg");
					}else
					{
						$headerPic = Yii::app()->params['avatarPath']."default.jpeg";
						rename($headerPic, Yii::app()->params['avatarPath'].$user_id.".jpeg");	
						copy(Yii::app()->params['avatarPath'].'../default.jpeg',$headerPic);		
					}
				}
				
	        	return $user_id;
	        }
    	}
    }
	
	/*
	* 保存扫码日志
	* 1) eventkey一定要是开启推广二维码的用户;
	* 2) 未付费;
	* 3) 上一次扫码记录已经超过72小时;
	* 满足以上四个条件，则记录扫码日志并确定继承关系
	* 在付费的时候如果还没有继承关系则归属公司
	*/
	public function saveScanLog($openid,$eventkey)
	{
		$yxInfo = UserInfoView::model()->find("user_id=:user_id",array(":user_id"=>$eventkey));
		if(empty($yxInfo) || $yxInfo->wx_off == 0)
		{
			return;
		}
		
		$userInfo = UserBaseInfo::model()->find("openid=:openid",array(":openid"=>$openid));
		if($userInfo->role_type == 1)
		{
			//已付费
			return;
		}	
		
		$scanFlag = true;
		$last = ScanQrcodeLog::model()->find(array("condition"=>"openid=:openid and qrcode_type='recommend'","order"=>"create_time desc","params"=>array(":openid"=>$openid)));
		if(!empty($last))
		{
			$lastTime = $last->create_time;
			$tmpTime = date("Y-m-d H:i:s",strtotime("$lastTime +3 days"));
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
			$old_c_from = $userInfo->c_from;
						
			//扫码有效  直接确定继承关系
			$userInfo->a_from = $eventkey;
			$userInfo->b_from = $yxInfo->a_from;				
			$userInfo->c_from = $yxInfo->b_from;
			$userInfo->save();
			
			//如果之前已经有继承关系且现在的关系跟以前不一样，
			//要重新计算老推荐人统计表的值
			if(!empty($old_a_from) && $old_a_from != $userInfo->a_from)
			{
				$this->refreeTongjiSubUpdate($old_a_from);
				if(!empty($old_b_from))
					$this->refreeTongjiSubUpdate($old_b_from);
				if(!empty($old_c_from))
					$this->refreeTongjiSubUpdate($old_c_from);	
			}
			
			//更新新推荐人统计表的值
			$this->refreeTongjiSubUpdate($userInfo->a_from);
			if(!empty($userInfo->b_from))
				$this->refreeTongjiSubUpdate($userInfo->b_from);
			if(!empty($userInfo->c_from))
				$this->refreeTongjiSubUpdate($userInfo->c_from);

			file_put_contents("ysy.txt","\r\n\r\n".date("Y-m-d H:i:s")."  ".$userInfo->user_id."扫码".$eventkey,FILE_APPEND);
			//扫码有效，给扫码人推客服消息			
			$contentStr = "恭喜您成功关注梦想汇\n";
			if(empty($yxInfo->real_name))
			{
				//真实姓名为空显示昵称
				$contentStr .= "您的推荐人是【".$yxInfo->nickname."】\n";
			}else{
				$contentStr .= "您的推荐人是【".$yxInfo->nickname."】,姓名:".$yxInfo->real_name.",手机号：".$yxInfo->phone."\n";
			}
			
			$contentStr .= "您想把手机变成印钞机吗？\n";
			$contentStr .= "您想成为微信营销高手月入数万吗？\n";	
			$contentStr .= "您想拥有百万高端客户瞬间实现财富核裂变吗？\n";	
			$contentStr .= "恭喜您来对了！\n";	
			$contentStr .= "赶紧进入平台，为您揭开财富核裂变的秘密。\n";	
			$contentStr .= "需要进群接受进一步辅导吗？请联系叶子老师：135-5268-0107，微信号：W1328670913；美丽老师：15049564792（手机和微信号）";	
			echo $this->creat_xml_response($contentStr);
			file_put_contents("ysy.txt",date("Y-m-d H:i:s")."  给扫码人发消息".$eventkey,FILE_APPEND);
			
			//给推荐人推消息
			$json1 = '{
				"touser":"'.$yxInfo->openid.'",
				"msgtype":"text",
				"text":
				{
					"content":"恭喜您，【'.$userInfo->nickname.'】通过扫描您的“推广二维码”关注天枭梦想汇，成为您的准1级营销总监，请赶紧跟进成交。特别提醒：关系锁定时间为72小时，72小时后未付款自动解除关系，再扫描其他人二维码成交将会计入别人的收益。"
				}
			}';
			$this->textmsg($json1);
			file_put_contents("ysy.txt",date("Y-m-d H:i:s")."  给一级推荐人发消息".$eventkey,FILE_APPEND);
			
			if(!empty($yxInfo->a_from) && $yxInfo->a_from != Yii::app()->params['renlfId'])
			{
				$twoInfo = UserBaseInfo::model()->find("user_id=:user_id",array(":user_id"=>$yxInfo->a_from));
				$json2 = '{
					"touser":"'.$twoInfo->openid.'",
					"msgtype":"text",
					"text":
					{
						"content":"恭喜您，【'.$userInfo->nickname.'】通过扫描您的1级营销总监【'.$yxInfo->nickname.'】关注天枭梦想汇，成为您的准2级业务主管，请赶紧跟进成交。特别提醒：关系锁定时间为72小时，72小时后未付款自动解除关系，再扫描其他人二维码成交将会计入别人的收益。"
					}
				}';
				$this->textmsg($json2);	
				file_put_contents("ysy.txt",date("Y-m-d H:i:s")."  给二级推荐人发消息".$eventkey,FILE_APPEND);
			}
			if(!empty($yxInfo->b_from) && $yxInfo->b_from != Yii::app()->params['renlfId'])
			{
				$threeInfo = UserBaseInfo::model()->find("user_id=:user_id",array(":user_id"=>$yxInfo->b_from));
				$json3 = '{
					"touser":"'.$threeInfo->openid.'",
					"msgtype":"text",
					"text":
					{
						"content":"恭喜您，【'.$userInfo->nickname.'】通过扫描您的2级业务主管【'.$yxInfo->nickname.'】关注天枭梦想汇，成为您的准3级销售代表，请赶紧跟进成交。特别提醒：关系锁定时间为72小时，72小时后未付款自动解除关系，再扫描其他人二维码成交将会计入别人的收益。"
					}
				}';
				$this->textmsg($json3);	
				file_put_contents("ysy.txt",date("Y-m-d H:i:s")."  给三级推荐人发消息".$eventkey,FILE_APPEND);				
			}
		}		
	}	
	
	//推广统计表数据插入或更新
	public function refreeTongjiSubUpdate($yx_from)
	{
		$tongji = RefreeTongji::model()->find("user_id=:user_id",array(":user_id"=>$yx_from));
		if(empty($tongji)){
			$tongji = new RefreeTongji();
			$tongji->user_id = $yx_from;
		}
		$subNum = UserBaseInfo::model()->count("a_from=:user_id or b_from=:user_id or c_from=:user_id",array(":user_id"=>$yx_from));
		$tongji->subNum = $subNum;
		$tongji->save();		
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
			
			if($userinfo->country == '中国'){
				$city = TblCity::model()->findCity($userinfo->city);
				$province = TblCity::model()->findCity($userinfo->province);				
				$city = $city['id'];
				$province = $province['id'];
			}else{
				$province = TblCity::model()->findCity($userinfo->country);				
				$city = '';
				$province = $province['id'];
			}
			$sex = $userinfo->sex;
			$usermodel = UserBaseInfo::model()->find("openid=:openid",array(":openid"=>$openid));
			$usermodel->nickname = filterSql($nickname);   //过滤特殊字符
			$usermodel->sex = $sex;
			$usermodel->province = $province;
			$usermodel->city = $city;
			
			if($usermodel->save())
			{
				$user_id = $usermodel->user_id;
				//个人信息保存成功   同步头像				
				if (!isset($userinfo->headimgurl)){
					$headerPic = Yii::app()->params['avatarPath']."default.jpeg";
					rename($headerPic, Yii::app()->params['avatarPath'].$user_id.".jpeg");	
					copy(Yii::app()->params['avatarPath'].'../default.jpeg',$headerPic);
				}else{
					$avartar = $this->getAvatar($userinfo->headimgurl, $userinfo->openid,$user_id);
					$headerPic = Yii::app()->params['avatarPath'].$userinfo->openid.".jpeg";
					if(file_exists($headerPic))
					{
						rename($headerPic, Yii::app()->params['avatarPath'].$user_id.".jpeg");
					}else
					{
						$headerPic = Yii::app()->params['avatarPath']."default.jpeg";
						rename($headerPic, Yii::app()->params['avatarPath'].$user_id.".jpeg");	
						copy(Yii::app()->params['avatarPath'].'../default.jpeg',$headerPic);		
					}
				}
			}else
				$returnMsg = '对不起，您的微信资料同步失败！请重新同步。';					
		}else
			$returnMsg = '对不起，您尚未关注天枭梦想汇，无法同步微信资料！';
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
	
	
	//组合欢迎消息，本篇为1个图文消息
	public function creat_img_welcome_response($openid,$userid)
	{
		$userInfo = UserBaseInfo::model()->find("user_id=:user_id",array(":user_id"=>$userid));
		$title1 = "尊敬的".$userInfo->nickname."，欢迎关注天枭梦想汇！";
			
		$time = time();
		$ArticleCount = "1";
		$desc1 = "天枭茶叶创始人/微信自我营销第一人/淘宝网十大网商/马云三次为其点赞/《微信营销108招》作者之一";
		$purl1 = Yii::app()->params['wxUrl']."/welcome/".$userid.".jpg?".Yii::app()->params['clearcache'];
		$urlaction = 'index';
		$state = '1000';
		$u = new MainUse();
		$url1 = $u->oauthUrl($urlaction,$state);
		
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
	
	
		$resultStr = sprintf($textTpl, $this->fromUsername, $this->toUsername, $time, 
				$title1,
				$desc1,
				$purl1,
				$url1
				);
		return $resultStr;
	}
	
	
	private function getClient($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);//
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// 使用自动跳转
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Errno'.curl_error($ch);//捕抓异常
			return;
		}
		curl_close($ch);
		$output=json_decode($output);
		return $output;
	}
	
	private function postClient($url,$post_data){//POST方法
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
	
	public  function getAvatar($url,$openid,$userid){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);//
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// 使用自动跳转
		curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 设置超时限制防止死循环
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$output = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Errno'.curl_error($ch);//捕抓异常
			return;
		}
		curl_close($ch);
		$avatarPath=Yii::app()->params['avatarPath'].$openid.".jpeg";
		$savatarPath=Yii::app()->params['smallAvatarPath'].$userid.".jpeg";
		$len = file_put_contents($avatarPath,$output);
		//微信头像如果是类型不是jpg格式的，要处理过才能获取下来    2015-01-10
		$arr = getimagesize($avatarPath);
		if($arr[2] == 1)
		{
			$avtar_img = imagecreatefromgif($avatarPath);
		}elseif($arr[2] == 3)
		{
			$avtar_img = imagecreatefrompng($avatarPath);
		}else
		{
			$avtar_img = imagecreatefromjpeg($avatarPath);
		}
		//生成缩略图头像
		$images = new SimpleImageHandle();
		$images->load($avatarPath);
		$images->resize(80,80);
		$images->save($savatarPath);
		return $avatarPath;
	}
	
	public function getAccess(){
		$param=array("appid"=>$this->appid,"secret"=>$this->secret,"grant_type"=>$this->grant_type);
		$get_args="";
		foreach ($param as $k=>$v){
			$get_args .= $k."=".$v."&";
		}
		$ass_url="https://api.weixin.qq.com/cgi-bin/token?";
		$ass_url .=$get_args;
		$output=$this->getClient($ass_url);
		$expire_time=time()+7000;
		file_put_contents($this->webTokenPath, $output->access_token);//写入ｔｏｋｅｎ文件
		//file_put_contents($this->timePath, time()+$output->expires_in);//写入过期时间
		file_put_contents($this->timePath, $expire_time);//写入过期时间
		return $output;
	}
	
	private function haveAccessToken(){
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
		$userUrl="https://api.weixin.qq.com/cgi-bin/user/info?";
		$param=array('access_token'=>$this->webToken,'openid'=>$openid);
		$get_args="";
		foreach ($param as $k=>$v){
			$get_args .= $k."=".$v."&";
		}
		$userUrl .=$get_args;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $userUrl);//
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
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
		
		$result = $this->postClient($url,$post_data);	
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
			Yii::log('create_qrcode_errno:'.curl_error($ch),'error');
			//echo 'Errno'.curl_error($ch);//捕抓异常
			return;
		}
		curl_close($ch);
		
		$codePath = Yii::app()->params['myQrCodePath'].$yx_from.".jpg";
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
	
	//下载多媒体文件  图片
	public function getMediaInfo($media_id){
		$this->haveAccessToken();
		$mediaUrl = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=".$this->webToken."&media_id=".$media_id;
		
		$ch = curl_init($mediaUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);//
		curl_setopt($ch, CURLOPT_NOBODY, 0);// 只取body头
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
		$output = curl_exec($ch);
		$httpinfo = curl_getinfo($ch);
		if (curl_errno($ch)) {
			//echo 'Errno'.curl_error($ch);//捕抓异常
			//return;
			return array('status'=>'error','msg'=>curl_error($ch));
		}
		curl_close($ch);
		//$imageAll = array_merge(array('header'=>$httpinfo),array('body'=>$output));
		
		//返回图片流
		return array('status'=>'success','msg'=>$output);
	}
	
	//上传临时素材
	public function uploadTempFile($type = 'image',$filepath)
	{
		$filedata = array("media"=>"@".$filepath);
		$this->haveAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$this->webToken."&type=".$type;
		$result = $this->postClient($url,$filedata);
		if(isset($result->media_id))
		{
			return $result->media_id;
		}
		return 'error';
	}
	
	//上传永久素材
	//例 description : array("title"=>"minion_01","introduction"=>"xx111")
	public function uploadForeverFile($type = 'image',$filepath,$description = array())
	{
		$filedata = array("media"=>"@".$filepath);
		if($type == 'video'){
			$filedata["description"] = json_encode($description);
		}
		$this->haveAccessToken();
		$url = "https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=".$this->webToken."&type=".$type;
		$result = $this->postClient($url,$filedata);
		if(isset($result->media_id))
		{
			return $result->media_id;
		}
		
		return 'error';
	}	
}





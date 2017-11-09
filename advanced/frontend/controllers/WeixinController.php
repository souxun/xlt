<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use common\helps\PublicFunction;
use common\helps\Weixin;
use common\helps\JSSDK;
use frontend\models\UserBaseInfo;
use frontend\models\TblCity;
use app\models\UserOrder;
use frontend\models\RefreeSettleBill;

//前端效果地址：https://pro.modao.cc/app/ARu1k2NKgAb6d6AjjfHkwxOiDETE0QA

class WeixinController extends Controller
{
	public $layout = false;
	//与微信交互关闭csrf验证
	public $enableCsrfValidation = false;
	private $url;
	private $PublicFunction;
	
	public function beforeAction($action)
	{	    
		$this->url = Yii::$app->params['appUrl'];
		$this->PublicFunction = new PublicFunction;
		if(parent::beforeAction($action))
		{
			return true;
		}
		return false;
	}

	/**
	 * 微信交互  关注、推送图文消息、文本消息、地理位置等
	 */
	public function actionIndex()
	{
		if (isset($GLOBALS["HTTP_RAW_POST_DATA"])){
			$postStr = ($GLOBALS["HTTP_RAW_POST_DATA"]);
		}else {
			$postStr = "";
		}
		$Weixin = new Weixin($postStr);
		$Weixin->SavePost();
		/*$Weixin = new Weixin('');
		echo $Weixin->valid($_GET['echostr'],$_GET['signature'],$_GET['timestamp'],$_GET['nonce']);*/
	}
	
	//微信菜单触发
	public function actionWxClient()
	{
		if((isset($_GET['code'])&& isset($_GET['state'])) || isset($_GET['openid']))
		{
			if(isset($_GET['code'])&& isset($_GET['state']))
			{
				$geturl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".Yii::$app->params['appid']."&secret=".Yii::$app->params['secret']."&code=".$_GET['code']."&grant_type=authorization_code";
				$arr = $this->PublicFunction->getClient($geturl);				
			}elseif(isset($_GET['openid'])){
				$json = '{"openid":"'.$_GET['openid'].'"}';
				$arr = json_decode($json);
			}

			if(isset($arr->openid))
			{
				$userinfo = UserBaseInfo::find()->select(['openid','user_id'])->where("openid=:openid",[":openid"=>$arr->openid])->one();
				if (!empty($userinfo))
				{
					//查询是否关注雪莲贴，已经关注的存session，跳转到关注图文消息
					$myWeixin = new Weixin('');
					$infoarr = $myWeixin->getUserInfo($userinfo->openid);
					if(isset($infoarr->subscribe_time) && 1 == $infoarr->subscribe)
					{						
						//没有购买的先购买
						if($userinfo->role_type == 0)
						{
							$this->redirect($this->url.'/xlt/Purchase?user_id='.$userinfo->user_id);	
						}
						//购买过直接跳转雪莲首页
						$this->redirect($this->url.'/xlt?user_id='.$userinfo->user_id);
						
					}else
					{
						//关注图文要重新设计过
						$this->redirect(Yii::$app->params['guanzuUrl']);
					}
				}else {
					throw new HttpException(400,"您尚未关注或者登录异常，请重新返回微信公众帐号点击平台登录");
				}
			}else {
				throw new HttpException(400,"您尚未关注或者登录异常，请重新返回微信公众帐号点击平台登录");
			}
		}else {
			throw new HttpException(400,"您尚未关注或者登录异常，请重新返回微信公众帐号点击平台登录");
		}	
	}
	
	//获取JSSDK签名
	public function actionGetJssdkSign($url = null)
	{
		if(!empty($url))
		{
			$jssdk = new JSSDK();
			$signPackage = $jssdk->getSignPackage($url);
			echo json_encode([
				"appId"=>$signPackage["appId"],
				"timestamp"=>$signPackage["timestamp"],
				"nonceStr"=>$signPackage["nonceStr"],
				"signature"=>$signPackage["signature"],
			]);			
		}
	}
	
	//支付包
	public function actionWxPay($user_id = null,$num = null)
	{
		if(!empty($user_id) && !empty($num))
		{	
			$userInfo = UserBaseInfo::find()->select(['user_id','openid','role_type'])->where('user_id=:user_id',[':user_id'=>$user_id])->one();
			if(empty($userInfo))
			{
				$return_res['code'] = '1005';
				$return_res['results'] = "user error";
				$return_res['msg'] = "user_id错误，用户不存在，请重新登陆。";
				echo json_encode($return_res);	
				Yii::$app->end();				
			}
			$num = $num+0;
			if($num == 0)
			{
				$return_res['code'] = '1006';
				$return_res['results'] = "num error";
				$return_res['msg'] = "数量必须大于0。";
				echo json_encode($return_res);	
				Yii::$app->end();				
			}
			if($userInfo->role_type == 0 && $num > 1)
			{
				$return_res['code'] = '1006';
				$return_res['results'] = "num error";
				$return_res['msg'] = "数量错误，非会员只能购买一套。";
				echo json_encode($return_res);	
				Yii::$app->end();
			}
			
			$money = Yii::$app->params['price']*$num;
			$millisecond = str_pad($this->PublicFunction->get_millisecond(),3,'0',STR_PAD_RIGHT);
			$out_trade_no = $userInfo->user_id.'_'.date("YmdHis").$millisecond;
			
			//插入订单表
			$UserOrder = new UserOrder;
			$UserOrder->attributes = [
				"user_id"=>$userInfo->user_id,
				"out_trade_no"=>$out_trade_no,
				"order_money"=>$money,
				"num"=>$num,				
			];	
			$UserOrder->save(true,["user_id","out_trade_no","order_money","num"]);
			
			$body = "购买雪莲贴分享装";
			$notifyUrl = Yii::$app->params['appUrl'].'/weixin/wxpay-result';
			$package = $this->getWxpayPackage($body,$out_trade_no,$money,$notifyUrl,$userInfo->user_id,$userInfo->openid);	
			echo $package;
			
		}else{
			$return_res['code'] = '1004';
			$return_res['results'] = "missing params";
			$return_res['msg'] = "缺少参数。";
			echo json_encode($return_res);			
		}
	}	
	
	/*
	* 微信支付包
	*/
	public function getWxpayPackage($body,$out_trade_no,$money,$notifyUrl,$user_id,$openid)
	{
		if(in_array($user_id,Yii::$app->params['testAccount']['wx_public']))
		{
			$money = 0.01;
		}
		//重置微支付配置文件
		$this->PublicFunction->resetWxpayConfig(Yii::$app->params['appid'],Yii::$app->params['mchid'],Yii::$app->params['key'],Yii::$app->params['secret']);			
		include_once(__DIR__ ."/../../common/helps/wxPay/lib/WxPay.JsApiPay.php");
		
		//统一支付下单
		$input = new \WxPayUnifiedOrder();
		$input->SetBody("$body");
		//$input->SetAttach("$attach");
		$input->SetOut_trade_no($out_trade_no);
		$input->SetTotal_fee($money*100);
		$input->SetNotify_url("$notifyUrl");
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openid);
		$orderResult = \WxPayApi::unifiedOrder($input);
		//接口返回 prepay_id，再次签名，生成支付数据包
		if(!array_key_exists("appid", $orderResult) || !array_key_exists("prepay_id", $orderResult) || $orderResult['prepay_id'] == "")
		{
			//单独记日志文件
			$this->PublicFunction->savePayLog('weixinPay.log','prepay_id错误:'.print_r($orderResult,true));
			
			return false;
		}
		$tools = new \JsApiPay();
		$jsApiParameters = $tools->GetJsApiParameters($orderResult);	
		return $jsApiParameters;		
	}
	
	/*
	* 微信支付异步回调
	*/	
	public function actionWxpayResult()
	{

		if(isset($GLOBALS["HTTP_RAW_POST_DATA"]))
		{
			$this->PublicFunction->savePayLog('weixinPay.log','wxCheckData:'.print_r($GLOBALS["HTTP_RAW_POST_DATA"],true));
			
			//重置微支付配置文件
			$this->PublicFunction->resetWxpayConfig(Yii::$app->params['appid'],Yii::$app->params['mchid'],Yii::$app->params['key'],Yii::$app->params['secret']);
			include_once(__DIR__ ."/../../common/helps/wxPay/PayNotifyCallBack.php");
			
			$notify = new \PayNotifyCallBack();
			$notify->Handle(false);
			if($notify->GetReturn_code() == 'FAIL')
			{
				//异常或验证签名错误或订单查询错误
				$this->PublicFunction->savePayLog('weixinPay.log','验证错误:'.$notify->GetReturn_msg());
				
				$notify->ReplyNotify(false);
				Yii::$app->end();
			}
			
			$notifyData = $notify->notifyData;

			//验证订单号有效性
			$bill = UserOrder::find()->select(['id','user_id','num','order_money','out_trade_no','status'])->where('out_trade_no=:out_trade_no',[":out_trade_no"=>$notifyData["out_trade_no"]])->one();
			if(empty($bill))
			{
				//订单错误
				$this->PublicFunction->savePayLog('weixinPay.log','充电订单错误');

				$notify->SetReturn_msg("订单错误");
				$notify->ReplyNotify(false);
				Yii::$app->end();
			}
			if($bill->status >= 1)
			{
				//订单已经支付成功，直接返回success
				$notify->ReplyNotify(false);
				Yii::$app->end();
			}
			//判断金额
			if(!in_array($bill->user_id,Yii::$app->params['testAccount']['wx_public']) && ($bill->order_money*100 != $notifyData["total_fee"]))
			{ 	
				$this->PublicFunction->savePayLog('weixinPay.log','订单金额错误'.$bill->money);
				
				$notify->ReplyNotify(false);
				Yii::$app->end();				
			}
			
			$transaction = UserOrder::getDb()->beginTransaction();
			try{								
				//更新订单状态，记录微信交易号
				$bill->pay_time = date("Y-m-d H:i:s");
				$bill->status = 1;
				$bill->trade_no = $notifyData["transaction_id"];
				if(!$bill->save(true,["pay_time","status","trade_no"]))
				{
					$errArr = array_values($bill->getErrors());
					throw new \Exception($errArr[0][0]);				
				}
				
				$userInfo = UserBaseInfo::find()->select(['id','nickname','role_type','a_from','b_from'])->where("user_id='".$bill->user_id."'")->one();

				//没有继承关系的归属公司

//				$transaction->commit();
				if(empty($userInfo->a_from))
                {
                    $userInfo->a_from = Yii::$app->params['companyId'];
                    $userInfo->save(false,['a_from']);
                }
                if($userInfo->role_type == 0)
                {
                    $userInfo->role_type = 1;
                    $userInfo->save(false,['role_type']);
                }

                //计算推荐人奖励，记录奖励明细表
                $userid = $bill->user_id;
                $num = $bill->num;
                $reward = Yii::$app->params['reward'];
                if($userInfo->a_from)
                {
                    $aReward = $reward[1]*$num;
                    $ARefreeSettleBill = new RefreeSettleBill;
                    $ARefreeSettleBill->user_id = $userid;
                    $ARefreeSettleBill->yx_from = $userInfo->a_from;
                    $ARefreeSettleBill->reward_money = substr(sprintf("%.3f",$aReward),0,-1);   //奖励金额结算到分
                    $ARefreeSettleBill->real_reward_money = $aReward;  //真实的金额保留
                    $ARefreeSettleBill->remark = '用户'.$userid.'('.$userInfo->nickname.')购买雪莲分享装'.$num.'套,A级推荐人'.$userInfo->a_from.'奖励'.$aReward.'元';
                    $ARefreeSettleBill->settle_type = 1;   //1A级推荐人奖励2B级3C级0无
                    $ARefreeSettleBill->out_trade_no = $bill->out_trade_no;

                    //给推荐人推消息
                    $a_UserInfo=UserBaseInfo::find()->select('openid')->where(['user_id'=>$userInfo->a_from])->one();
                    $json1 = '{
				"touser":"'.$a_UserInfo->openid.'",
				"msgtype":"text",
				"text":
				{
					"content":"您的准1级营销总监【'.$userInfo->nickname.'】成功付款正式成为您的1级营销总监。特别提醒：请多关注您新加入的小伙伴，让他/她更清楚分享模式以及雪莲贴对身体的好处，去分享健康收获财富。"
				}
			}';
                    (new Weixin(''))->textmsg($json1);

                    $ARefreeSettleBill->save();
                }
                //二级奖励要有条件(直推人和自己下单4套才可以有)

                if($userInfo->b_from)
                {
                    $tmpInfo = UserBaseInfo::find()->select("user_id")->where("a_from='".$userInfo->b_from."'")->all();
                    $users = "'".$userInfo->b_from."'";
                    foreach($tmpInfo as $v)
                    {
                        $users .= ",'".$v->user_id."'";
                    }
                    $totalNum = UserOrder::find()->where("user_id in(".$users.") and status>0")->sum('num');
                    if($totalNum >= 5)
                    {
                        $bReward = $reward[2]*$num;
                        $BRefreeSettleBill = new RefreeSettleBill;
                        $BRefreeSettleBill->user_id = $userid;
                        $BRefreeSettleBill->yx_from = $userInfo->b_from;
                        $BRefreeSettleBill->reward_money = substr(sprintf("%.3f",$bReward),0,-1);   //奖励金额结算到分
                        $BRefreeSettleBill->real_reward_money = $bReward;  //真实的金额保留
                        $BRefreeSettleBill->remark = '用户'.$userid.'('.$userInfo->nickname.')购买雪莲分享装'.$num.'套,B级推荐人'.$userInfo->b_from.'奖励'.$bReward.'元';
                        $BRefreeSettleBill->settle_type = 2;   //1A级推荐人奖励2B级3C级0无
                        $BRefreeSettleBill->out_trade_no = $bill->out_trade_no;

                         //给推荐人推消息
                     $a_UserInfo=UserBaseInfo::find()->select('nickname')->where(['user_id'=>$userInfo->a_from])->one();
                    $b_UserInfo=UserBaseInfo::find()->select('openid')->where(['user_id'=>$userInfo->b_from])->one();

                    $json1 = '{
				"touser":"'.$b_UserInfo->openid.'",
				"msgtype":"text",
				"text":
				{
					"content":"您的准2级业务主管【'.$userInfo->nickname.'】成功付款正式成为您的2级业务主管。特别提醒：请让您的1级营销总监【'.$a_UserInfo->nickname.'】多关注您新加入的小伙伴，让他/她更清楚分享模式以及雪莲贴对身体的好处，去分享健康收获财富
"
				}
			}';
                    (new Weixin(''))->textmsg($json1);

                        $BRefreeSettleBill->save();
                    }
                }

            $transaction->commit();
            }catch(\Exception $e)
			{
				$this->PublicFunction->savePayLog('weixinPay.log','catch abnormal:'.$e->getMessage());
				$transaction->rollBack();				
			}					
			
			$notify->ReplyNotify(false);
		}
	}		
}

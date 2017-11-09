<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use common\helps\PublicFunction;
use common\helps\Weixin;
use frontend\models\UserBaseInfo;


class MycodeController extends Controller
{
	public $layout = false;
	//与微信交互关闭csrf验证
	public $enableCsrfValidation = false;	
	
	
	/*
	*二维码继承关系确认(微信网页授权回调)处理
	*
	*/
	public function actionCodeCallback($yx_from = 0)
	{
		if (isset($_GET['code'])&& isset($_GET['state']))
		{
			$geturl = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".Yii::$app->params['appid']."&secret=".Yii::$app->params['secret']."&code=".$_GET['code']."&grant_type=authorization_code";
			$PublicFunction = new PublicFunction();
			$arr = $PublicFunction->getClient($geturl);
			if(isset($arr->openid))
			{
				//记录扫码日志
				$myWeixin = new weixin('');
				$myWeixin->saveScanLog($arr->openid,$yx_from);			
				
				$userinfo = UserBaseInfo::find()->select(['openid','user_id','role_type'])->where("opneid=:openid",[":openid"=>$arr->openid])->one();
				if(!empty($userinfo))
				{										
					//查询是否关注雪茄贴，已经关注的存session，并跳转到雪莲首页，没有关注的，跳转到关注图文消息
					$infoarr = $myWeixin->getUserInfo($userinfo->openid);
					if(isset($infoarr->subscribe_time) && 1 == $infoarr->subscribe)
					{
						//没有购买的先购买
						if($userinfo->role_type == 0)
						{
							$this->redirect(Yii::$app->params['appUrl'].'/#/Purchase?user_id='.$userinfo->user_id);
						}
						//购买过直接跳转雪莲首页
						$this->redirect(Yii::$app->params['appUrl'].'/#/?user_id='.$userinfo->user_id);		
					}
				}
			}
		}
		//不管扫描成功与否都跳转到提示关注图文消息，有些手机有可能出现扫码之后空白页的情况
		$this->redirect(Yii::$app->params['guanzuUrl']);
	}	

}
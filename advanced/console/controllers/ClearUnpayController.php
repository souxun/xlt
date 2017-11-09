<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use app\models\UserOrder;

//清除24小时内未付款的订单
class ClearUnpayController extends Controller
{	
	//执行命令  yii clear-unpay
	public function actionIndex()
	{
		$end_time = date("Y-m-d 00:00:00",strtotime("-2 days"));
		UserOrder::deleteAll("status=0 and create_time<'".$end_time."'");
	}
}
<?php

namespace frontend\controllers;

use app\models\PeopleMoneyExchangeBill;

use app\models\PeopleUserAddress;
use app\models\UserOrder;
use frontend\models\XltData;
use frontend\models\Qa;
use frontend\models\RefreeSettleBill;
use frontend\models\TblCity;
use frontend\models\UserBaseInfo;
use frontend\models\TixianBills;
use common\helps\Weixin;
use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;


class DreamsController extends Controller
{
    public $enableCsrfValidation = false;
    private $postData;
//    private $postData;


    public function beforeAction($action)
    {
        header('Access-Control-Allow-Origin:*');
        $this->postData = Yii::$app->request->post();
//        Yii::$app->postData->open();
//        $this->postData = Yii::$app->postData;
//        $this->postData['user_id'] = $this->postData['user_id'];
        if (parent::beforeAction($action)) {
            return true;
        }
        return false;
    }



    //1.我  首页
    public function actionIndex()
    {
        if (isset($this->postData['user_id'])) {
            $user_id = $this->postData['user_id'];
            $UserAppInfo = UserBaseInfo::find()
                ->select(['user_id', 'nickname','role_type'])
                ->where('user_id=:user_id', [':user_id' => $user_id])
                ->one();
            if (empty($UserAppInfo)) {
                $return_res['code'] = '1005';
                $return_res['results'] = "no exist";
                $return_res['msg'] = "用户id不存在";
                echo json_encode($return_res);
                Yii::$app->end();
            }

            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] = [
                'user_id' => $UserAppInfo->user_id,
                'nickname' => $UserAppInfo->nickname,
                'role_type'=>$UserAppInfo->role_type,
                'image'=>Yii::$app->params['outPath'].$user_id.".jpeg",
            ];
            echo json_encode($return_res);
            Yii::$app->end();
        } else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }


     //同步微信资料
    public function actionPerfectInfo()
    {
        if (isset($this->postData ['user_id']) && !empty($this->postData ['user_id'])) {
            $user_id = $this->postData['user_id'];
            $UserDetailInfo = UserBaseInfo::find()->where("user_id=:user_id", array(":user_id" => $user_id))->one();
            if (empty($UserDetailInfo)) {
                $return_res['code'] = '1003';
                $return_res['results'] = "not exist";
                $return_res['msg'] = "用户资料不存在。";
                echo json_encode($return_res);
                Yii::$app->end();
            }

            $data=(new Weixin(''))->syncWeixinInfo2($UserDetailInfo->openid);
            $UserInfo = UserBaseInfo::find()->select(['user_id','nickname','sex','province','city'])->where("user_id=:user_id", array(":user_id" => $user_id))->asArray()->one();
            if($data===true){
                $return_res['code'] = '0';
                $return_res['results'] = "success";
                $return_res['msg'] =[
                    'user_id' => $UserInfo['user_id'],
                    'nickname' => $UserInfo['nickname'],
                    'sex'=>$UserInfo['sex'],
                    'address'=>(new TblCity)->getCityName($UserInfo['province']).(new TblCity)->getCityName($UserInfo['city']),
                    'image'=>Yii::$app->params['outPath'].$user_id.".jpeg",
                ];
                echo json_encode($return_res);
                Yii::$app->end();
            }else{
                $return_res['code'] = '1001';
                $return_res['results'] = "failed";
                $return_res['msg'] =$data;
                echo json_encode($return_res);
                Yii::$app->end();
            }
        } else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }



    /**
     * 新增/修改地址
     */
    public function actionSaveAddress()
    {
        $model = new PeopleUserAddress();
        if ($model->load($this->postData, '') && $model->validate()) {
            $user_id = $this->postData['user_id'];
            $user = UserBaseInfo::find()->where(['user_id' => $user_id])->exists();
            if (!$user) {
                $return_res['code'] = '1001';
                $return_res['results'] = "user_id errod";
                $return_res['msg'] = "用户ID不存在";
                echo json_encode($return_res);
                Yii::$app->end();
            }

            $addressInfo = PeopleUserAddress::find()->where(['user_id' => $this->postData['user_id']])->one();
            if (empty($addressInfo)) {
                $model->save(false);
                $return_res['code'] = '0';
                $return_res['results'] = "success";
                $return_res['msg'] = '新增成功';
                echo json_encode($return_res);
                Yii::$app->end();
            } else {
                $addressInfo->attributes = $this->postData;
                if (!$addressInfo->save()) {
                //插入地址表错误
                $errArr = array_values($addressInfo->getErrors());
                $return_res['code'] = '1007';
                $return_res['results'] = "insert address error";
                $return_res['msg'] = $errArr[0][0];
                echo json_encode($return_res);
                Yii::$app->end();
            }
                $addressInfo->save(false);
                $return_res['code'] = '0';
                $return_res['results'] = "success";
                $return_res['msg'] = '修改成功';
                echo json_encode($return_res);
                Yii::$app->end();
            }
        } else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = $model->errors;
            echo json_encode($return_res);
        }
    }

    /**
     * 获取地址
     */
    public function actionGetAddress(){
        if (isset($this->postData['user_id'])){
            $user_id = $this->postData['user_id'];
            $address=PeopleUserAddress::find()->select(['name','phone','province','city','country','detail_address'])->where(['user_id'=>$user_id])->asArray()->one();
            if(empty($address)){
                $return_res['code'] = '1002';
                $return_res['results'] = "failed";
                $return_res['msg'] ='没有地址';
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] =$address;
            echo json_encode($return_res);
        }else{
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] ='缺少参数';
            echo json_encode($return_res);
        }
    }

    //我的订单
    public function actionMyOrder()
    {
        if (isset($this->postData['user_id'])) {
            $user_id = $this->postData['user_id'];
            $user = UserBaseInfo::find()->where(['user_id' => $user_id])->exists();
            if (!$user) {
                $return_res['code'] = '1001';
                $return_res['results'] = "user_id errod";
                $return_res['msg'] = "用户ID不存在";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $orderInfo = UserOrder::find()->where(['user_id' => $user_id,'status'=>[1,2,3,4]])->orderBy(['pay_time'=>SORT_DESC])->asArray()->all();
            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] = $orderInfo;
            echo json_encode($return_res);
            Yii::$app->end();
        } else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }

    //-----------------------------------------我的订单 end



    //-----------------------------------------收货地址保存 end


    //我的团队
    public function actionMyComrades($user_id = 0)
    {
        if (isset($this->postData['user_id'])) {
            $user_ids = $this->postData['user_id'];
            //查询A B会员数，当日人数、总人数、已付费人数及金额
            $user_id = empty($user_id) ? $user_ids : $user_id;
            $info=UserBaseInfo::find()->where(['user_id'=>$user_id])->exists();
            if(!$info){
                $return_res['code'] = '1002';
                $return_res['results'] = "user not exists";
                $return_res['msg'] = "该用户不存在";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $result = [];
            //当日人数
            $result['currDayAMember'] = UserBaseInfo::find()->where('a_from=:user_id and date(create_time)=curdate()', array(":user_id" => $user_id))->count();

            $result['currDayBMember'] = UserBaseInfo::find()->where('b_from=:user_id and date(create_time)=curdate()', array(":user_id" => $user_id))->count();
            //$result['currDayCMember'] = UserBaseInfo::find()->where('c_from=:user_id and date(create_time)=curdate()', array(":user_id" => $user_id))->count();

            //总人数
            $result['totalAMember'] = UserBaseInfo::find()->where('a_from=:user_id', array(":user_id" => $user_id))->count();
            $result['totalBMember'] = UserBaseInfo::find()->where('b_from=:user_id', array(":user_id" => $user_id))->count();

//            $result['totalCMember'] = UserBaseInfo::find()->where('c_from=:user_id', array(":user_id" => $user_id))->count();
            //战友团总计
            $result['totalMember'] = UserBaseInfo::find()->where('a_from=:user_id or b_from=:user_id', array(":user_id" => $user_id))->count();

            //今日新增总计
//            $result['currDayMember'] = UserBaseInfo::find()->where('date(create_time)=curdate() and (a_from=:user_id or b_from=:user_id or c_from=:user_id)', array(":user_id" => $user_id))->count();

            //付费用户总计
            $result['totalPayMember'] = UserBaseInfo::find()->where('role_type in(1,2) and (a_from=:user_id or b_from=:user_id)', array(":user_id" => $user_id))->count();
            $result['payRate'] = 0;
            $result['unpayRate'] = 0;
            if ($result['totalMember'] > 0) {
                $result['payRate'] = round($result['totalPayMember'] / $result['totalMember'], 3) * 100;
                $result['unpayRate'] = 100 - $result['payRate'];
            }

            //各级佣金统计
            //总推荐佣金
            $sql = 'SELECT SUM(reward_money) as reward_money from refree_settle_bill WHERE yx_from=:user_id';
            $billInfo = RefreeSettleBill::findBySql($sql, [":user_id" => $user_id])->one();
            $result['totalRewardMoney'] = $billInfo->reward_money + 0;

            //A级推荐佣金
            $sql = 'SELECT SUM(reward_money) as reward_money from refree_settle_bill WHERE yx_from=:user_id and settle_type=1 ';
            $abillInfo = RefreeSettleBill::findBySql($sql, [":user_id" => $user_id])->one();
            $result['totalARewardMoney'] = $abillInfo->reward_money + 0;

            //B级推荐佣金
            $sql = 'SELECT SUM(reward_money) as reward_money from refree_settle_bill WHERE yx_from=:user_id and settle_type=2 ';
            $bbillInfo = RefreeSettleBill::findBySql($sql, [":user_id" => $user_id])->one();
            $result['totalBRewardMoney'] = $bbillInfo->reward_money + 0;

//            $sql = 'SELECT SUM(reward_money) as reward_money from people_refree_settle_bill WHERE yx_from=:user_id and settle_type=3 ';
//            $cbillInfo = RefreeSettleBill::findBySql($sql, [":user_id" => $user_id])->one();
//            $result['totalCRewardMoney'] = $cbillInfo->reward_money + 0;

            $result['Arate'] = 0;
            $result['Brate'] = 0;
            if ($result['totalRewardMoney'] > 0) {
                $result['Arate'] = round($result['totalARewardMoney'] / $result['totalRewardMoney'], 3) * 100;
                $result['Brate'] = round($result['totalBRewardMoney'] / $result['totalRewardMoney'], 3) * 100;
            }

            //平均转化率
            $allPayNum=UserBaseInfo::find()->where(['role_type'=>1])->count();
            $allNum=UserBaseInfo::find()->count();
            $avgRate=round($allPayNum/$allNum,3)*100;


            $return_res['code'] = "0";
            $return_res['results'] = "success";
            $return_res['msg'] = [
                'currDayAMember' => $result['currDayAMember'],
                'currDayBMember' => $result['currDayBMember'],
                'totalAMember' => $result['totalAMember'],
                'totalBMember' => $result['totalBMember'],
                'payRate' => $result['payRate'],
                'unpayRate' => $result['unpayRate'],
                'payMember' => $result['totalPayMember'],
                'unpayMember' => $result['totalMember']-$result['totalPayMember'],
                'Arate' => $result['Arate'],
                'Brate' => $result['Brate'],
               /* 'CRate'=>$result['payRate']==0?0:
                    round($result['payRate']/$result['unpayRate'],3)*100,*/
                'CRate'=>$result['payRate'],
                'avgRate'=>$avgRate,
                'totalARewardMoney' => $result['totalARewardMoney'],
                'totalBRewardMoney' => $result['totalBRewardMoney'],
            ];
            echo json_encode($return_res);
        } else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }


    }

    /**
     * 我的A级营销总监
     */
    public function actionMyAComrades()
    {
        if (isset($this->postData['page']) && isset($this->postData['pagesize']) && isset($this->postData['user_id'])) {
            $user_id = $this->postData['user_id'];
            $userInfo = UserBaseInfo::find()->where("user_id=:user_id", [":user_id" => $user_id])->one();
            if (empty($userInfo)) {
                $return_res['code'] = '1005';
                $return_res['results'] = "no exists";
                $return_res['msg'] = "user_id不存在,请重新输入";
                echo json_encode($return_res);
                Yii::$app->end();
            }

            $page = $this->postData['page'] + 0;
            $pagesize = $this->postData['pagesize'] + 0;
            $pagestart = 0;
            if ($page > 0) {
                $pagestart = ($page - 1) * $pagesize;
            }
//            $myAComrades = UserBaseInfo::find()->with(['tongji'])->select(['nickname', 'create_time','province','city','user_id','sex'])->where(['a_from' => $user_id,])->offset($pagestart)->limit($pagesize)->createCommand()->getRawSql();
            if(isset($this->postData['type'])){
                $sql='SELECT user_base_info.user_id,nickname,sex,province,city,role_type,user_base_info.create_time,subNum,payNum FROM user_base_info left join refree_tongji on( user_base_info.user_id = refree_tongji.user_id)  where user_base_info.a_from =:user_id ORDER BY `subNum` DESC,`role_type` DESC'.' limit '.$pagestart.','.$pagesize;
            }else{
                $sql='SELECT user_base_info.user_id,nickname,sex,province,city,role_type,user_base_info.create_time,subNum,payNum FROM user_base_info left join refree_tongji on( user_base_info.user_id = refree_tongji.user_id)  where user_base_info.a_from =:user_id '.' limit '.$pagestart.','.$pagesize;
            }
            $myAComrades=ActiveRecord::findBySql($sql,['user_id'=>$user_id])->asArray()->all();

            $myASubNum=UserBaseInfo::find()->where(['a_from'=>$user_id])->count();
            $myAPayNum=UserBaseInfo::find()->where(['a_from'=>$user_id,'role_type'=>1])->count();
            foreach ($myAComrades as &$k){
                $k['image']='';
                $k['province']=(new TblCity)->getCityName($k['province']);
                $k['city']=(new TblCity)->getCityName($k['city']);
                $k['subNum']=$k['subNum']==NULL?0:$k['subNum'];
                $k['payNum']=$k['payNum']==NULL?0:$k['payNum'];
                $k['image']=Yii::$app->params['outPath'].$k['user_id'].".jpeg";
                $k['role_type'];
            }


            $return_res['code'] = '0';
            $return_res['row'] = "";
            $return_res['subNum']=$myASubNum;
            $return_res['payNum']=$myAPayNum;
            $return_res['msg'] = $myAComrades;
            echo json_encode($return_res);
        } else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }

    /**
     * 我的B级营销总监
     */
    public function actionMyBComrades()
    {
        if (isset($this->postData['page']) && isset($this->postData['pagesize']) && isset($this->postData['user_id'])) {
            $user_id = $this->postData['user_id'];
            $userInfo = UserBaseInfo::find()->where("user_id=:user_id", [":user_id" => $user_id])->one();
            if (empty($userInfo)) {
                $return_res['code'] = '1005';
                $return_res['results'] = "no exists";
                $return_res['msg'] = "user_id不存在,请重新输入";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $page = $this->postData['page'] + 0;
            $pagesize = $this->postData['pagesize'] + 0;
            $pagestart = 0;
            if ($page > 0) {
                $pagestart = ($page - 1) * $pagesize;
            }
//            $myAComrades = UserBaseInfo::find()->select(['nickname', 'create_time','province','city','user_id','sex'])->where(['b_from' => $user_id,])->offset($pagestart)->limit($pagesize)->asArray()->all();

            if(isset($this->postData['type'])){
                $sql='SELECT user_base_info.user_id,nickname,sex,province,city,role_type,user_base_info.create_time,subNum,payNum FROM user_base_info left join refree_tongji on( user_base_info.user_id = refree_tongji.user_id)  where user_base_info.b_from =:user_id ORDER BY `subNum` DESC,`role_type` DESC'.' limit '.$pagestart.','.$pagesize;
            }else{
                $sql='SELECT user_base_info.user_id,nickname,sex,province,city,role_type,user_base_info.create_time,subNum,payNum FROM user_base_info left join refree_tongji on( user_base_info.user_id = refree_tongji.user_id)  where user_base_info.b_from =:user_id '.' limit '.$pagestart.','.$pagesize;
            }
                $myAComrades=ActiveRecord::findBySql($sql,['user_id'=>$user_id])->asArray()->all();

            $myASubNum=UserBaseInfo::find()->where(['b_from'=>$user_id])->count();
            $myAPayNum=UserBaseInfo::find()->where(['b_from'=>$user_id,'role_type'=>1])->count();
            foreach ($myAComrades as &$k){
                $k['image']='';
                $k['province']=(new TblCity)->getCityName($k['province']);
                $k['city']=(new TblCity)->getCityName($k['city']);
                $k['subNum'] = $k['subNum'] == NULL ? 0 : $k['subNum'];
                $k['payNum'] = $k['payNum'] == NULL ? 0 : $k['payNum'];
                $k['image']=Yii::$app->params['outPath'].$k['user_id'].".jpeg";
                $k['role_type'];
            }
            $return_res['code'] = '0';
            $return_res['row'] = "";
            $return_res['subNum']=$myASubNum;
            $return_res['payNum']=$myAPayNum;
            $return_res['msg'] = $myAComrades;
            echo json_encode($return_res);
        } else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }

    // 我的推荐人详情 start
    public function actionMyLeader()
    {
        if (isset($this->postData['user_id'])) {
            $user_id = $this->postData['user_id'];
            $userInfo = UserBaseInfo::find()->where("user_id=:user_id", [":user_id" => $user_id])->one();
            if (empty($userInfo)) {
                $return_res['code'] = '1005';
                $return_res['results'] = "no exists";
                $return_res['msg'] = "user_id不存在,请重新输入";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $leaderAddress = PeopleUserAddress::find()->where("user_id=:user_id", array(":user_id" => $userInfo->a_from))->one();
            if (empty($leaderAddress)) {
                $return_res['code'] = '1007';
                $return_res['results'] = "address no exists";
                $return_res['msg'] = "address不存在,请重新输入";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $leaderInfo = UserBaseInfo::find()->select(['user_id', 'province', 'city', 'nickname'])->where("user_id=:user_id", [":user_id" => $userInfo->a_from])->one();

            if (empty($leaderInfo)) {
                $return_res['code'] = '1006';
                $return_res['results'] = "user error";
                $return_res['msg'] = "查询用户错误";
                echo json_encode($return_res);
                Yii::$app->end();
            }

            $return_res['code'] = '0';
            $return_res['results'] = "我的推荐人详情";
            $return_res['msg'] = [
                'nickname' => $leaderInfo->nickname,
                'province' => $leaderAddress['province'] . $leaderAddress['city'] . $leaderAddress['country'],
                'image'=>Yii::$app->params['outPath'].$userInfo->a_from.".jpeg",
            ];
            echo json_encode($return_res);
        } else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }

    }

    // --------------------------------

    //我的战友团会员列表??????
    public function actionMemberList($type, $u)
    {
        //累计总数、今日新增
        switch ($type) {
            case 'A':
                $where = 'a_from=:user_id';
                $whereDay = $where . ' and role_type>0';
                break;
            case 'B':
                $where = 'b_from=:user_id';
                $whereDay = $where . ' and role_type>0';
                break;
            case 'C':
                $where = 'c_from=:user_id';
                $whereDay = $where . ' and role_type>0';
                break;
        }
        $totalMember = UserBaseInfo::find()->where("$where", array(":user_id" => $u))->count();
        $dayMember = UserBaseInfo::find()->where("$whereDay", array(":user_id" => $u))->count();
        $user_id = $this->postData['user_id'];
        $userInfo = UserBaseInfo::find()->where("user_id=:user_id", array(":user_id" => $user_id))->count();


    }



    /**
     * +我的金库
     */
    public function actionMyCashBox(){
        if (isset($this->postData['page']) && isset($this->postData['pagesize']) && isset($this->postData['user_id'])) {
            $user_id = $this->postData['user_id'];
            $CurrentCount=RefreeSettleBill::find()->select(['reward_money'])->where(['yx_from'=>$user_id])->count();
//            $CurrentMoney=RefreeSettleBill::find()->select(['reward_money'])->where(['yx_from'=>$user_id])->sum('reward_money');
            $CurrentMoney=$this->getWithdrawMoney($user_id);
        /*    $AlreadyCount=PeopleMoneyExchangeBill::find()->select(['exchange_money'])->where(['user_id'=>$user_id])->count();*/
            $AlreadyCount=TixianBills::find()->select(['money'])->where(['user_id'=>$user_id,'status'=>1])->count();
//            $AlreadyMoney=PeopleMoneyExchangeBill::find()->select(['exchange_money'])->where(['user_id'=>$user_id])->sum('exchange_money');
            $AlreadyMoney=TixianBills::find()->select(['money'])->where(['user_id'=>$user_id,'status'=>1])->sum('money');
            $page = $this->postData['page'] + 0;
            $pagesize = $this->postData['pagesize'] + 0;
            $pagestart = 0;
            if ($page > 0) {
                $pagestart = ($page - 1) * $pagesize;
            }

            $query='SELECT `reward_money`, `create_time` FROM `refree_settle_bill` where yx_from=:user_id  ORDER BY create_time desc limit '.$pagestart.','.$pagesize;

            $record=ActiveRecord::findBySql($query,[':user_id'=>$user_id])->asArray()->all();
//            $lastRecord=PeopleMoneyExchangeBill::find()->select(['create_time',])->where('user_id=:user_id',[':user_id'=>$user_id])->orderBy(['create_time'=>SORT_DESC])->asArray()->one();

            $return_res['code'] = '0';
            $return_res['UnCount'] =$CurrentCount;
            $return_res['CurrentMoney'] =$CurrentMoney==null?0:$CurrentMoney;
            $return_res['IncomeCount'] =$AlreadyCount;
            $return_res['AlreadyMoney'] =$AlreadyMoney==null?0:$AlreadyMoney;
            $return_res['msg'] = $record;
            echo json_encode($return_res);
        }else {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }



    /**
     * 获取百答百答数据
     */
    public function actionQa()
    {
        $qa = Qa::getQaAll();
        if ($qa) {
            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] = $qa;
            echo json_encode($return_res);
            Yii::$app->end();
        } else {
            $return_res['code'] = '1001';
            $return_res['results'] = "failed";
            $return_res['msg'] = '服务器内部错误';
            echo json_encode($return_res);
        }
    }

    /**
     * 获取雪莲贴素材
     */
    public function actionGetData(){
        $model=new XltData;
        if($model->load($this->postData,'') && $model->validate()){
            $info=XltData::find()->select(['name','phone'])->where(['user_id'=>$this->postData['user_id']])->asArray()->one();
            if(!empty($info)){
                $return_res['code'] = '1002';
                $return_res['results'] = "failed";
                $return_res['msg'] = '该用户信息已存在';
                echo json_encode($return_res);
                Yii::$app->end();
            }
            if($model->save(false)){
            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] = "成功";
            echo json_encode($return_res);
        }
        }else{
            $return_res['code'] = '1001';
            $return_res['results'] = "failed";
            $return_res['msg'] = $model->errors;
            echo json_encode($return_res);
        }
    }

    /**
     * 雪莲贴素材展示
     */
    public function actionXltData(){
        if(isset($this->postData['user_id'])){
            $info=XltData::find()->select(['name','phone','user_id'])->where(['user_id'=>$this->postData['user_id']])->asArray()->one();
            if(!empty($info)){
                $return_res['code'] = '0';
                $return_res['results'] = "success";
                $return_res['msg'] = $info;
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $return_res['code'] = '1002';
            $return_res['results'] = "failed";
            $return_res['msg'] = '没有该数据';
            echo json_encode($return_res);
        }else{
            $return_res['code'] = '1001';
            $return_res['results'] = "failed";
            $return_res['msg'] = '缺少参数';
            echo json_encode($return_res);
        }
    }

    public function actionQaSearch(){
        $key=$this->postData['key'];
        $info=Qa::find()->select(['question','answer'])->where(['like','question',$key])->asArray()->all();
        $return_res['code'] = '0';
        $return_res['results'] = "success";
        $return_res['msg'] = $info;
        echo json_encode($return_res);
        Yii::$app->end();
    }





    /*
    * 收益提现
    * @param user_id:user_id
    * @param nickname:姓名
    * @param money:金额
    * @param type:提现方式
    * @param account:账号
    * @param phone:手机号
    */
    public function actionProfitTixianNew()
    {

        if(isset($this->postData['user_id']) && isset($this->postData['nickname']) && isset($this->postData['money']) && isset($this->postData['type']) && isset($this->postData['account'])&& isset($this->postData['phone']))
        {
            $user_id = $this->postData['user_id'];
            $UserBaseInfo = UserBaseInfo::find()->select('user_id')->where('user_id=:user_id',[':user_id'=>$user_id])->one();
            if(empty($UserBaseInfo))
            {
                $return_res['code'] = '1005';
                $return_res['results'] = "user error";
                $return_res['msg'] = "user_id错误，用户不存在，请重新登陆。";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $user_id = $UserBaseInfo->user_id;
            $nickname = trim($this->postData['nickname']);
            $money = $this->postData['money']+0;
            $type = $this->postData['type']+0;
            $phone =$this->postData['phone'];
            $account = trim($this->postData['account']);
            if(empty($nickname))
            {
                $return_res['code'] = '1006';
                $return_res['results'] = "name error";
                $return_res['msg'] = "真实姓名不能为空。";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            if(empty($account))
            {
                $return_res['code'] = '1007';
                $return_res['results'] = "account error";
                $return_res['msg'] = "账号不能为空。";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            if(empty($phone))
            {
                $return_res['code'] = '1008';
                $return_res['results'] = "phone error";
                $return_res['msg'] = "手机号不能为空。";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            //
            //可提现金额
            $withdrawMoney = $this->getWithdrawMoney($user_id);

//
            if($money < 100 || $money > $withdrawMoney)
            {
                $return_res['code'] = '1008';
                $return_res['results'] = "money error";
                $return_res['msg'] = "提现金额必须大于100且小于可提现金额。";
                echo json_encode($return_res);
                Yii::$app->end();
            }

            //插入提现明细表
            $bill = new TixianBills;
            $bill->attributes = array(
                "user_id"=>$user_id,
                "money"=>$money,
                "type"=>$type,
                "account"=>$account,
                "nickname"=>$nickname,
                'phone'=>$phone,
            );
            if(!$bill->save())
            {
                $errArr = array_values($bill->getErrors());
                $return_res['code'] = '1009';
                $return_res['results'] = "insert tixian error";
                $return_res['msg'] = $errArr[0][0];
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $withdrawMoney = $this->getWithdrawMoney($user_id);
            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] = [
                'user_id' => $bill->user_id,
                'nickname' => $bill->nickname,
                "money"=>$withdrawMoney,
                'type'=>$bill->type,
                'account'=>$bill->account,
                'phone'=>$bill->phone,
            ];
            echo json_encode($return_res);
        }else
        {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }

    /*
	* 获取可提现余额公共方法
	*/
    public function getWithdrawMoney($user_id)
    {
        $rewardMoney = 0;
        //可提现余额
        $refreeSettleBill=RefreeSettleBill::find()->select(['real_reward_money'])->where(['yx_from'=>$user_id])->sum('real_reward_money');
        //已经提现金额
        $tixianInfo = TixianBills::find()->select(["sum(money) as money"])->where("user_id=:user_id",[":user_id"=>$user_id])->one();
        $alreadyMoney = intval($tixianInfo['money']);
        return intval($refreeSettleBill)-$alreadyMoney;

    }

    /*
    * 提现记录
    * @param user_id:user_id
    * @param page:第几页开始
    * @param pagesize:查询几条
    */
    public function actionGetTixianList()
    {
        if(isset($this->postData['user_id']) && isset($this->postData['page']) && isset($this->postData['pagesize']))
        {   $user_id = $this->postData['user_id'];
            $UserBaseInfo = UserBaseInfo::find()->select(['user_id'])->where('user_id=:user_id',[':user_id'=>$user_id])->one();
            if(empty($UserBaseInfo))
            {
                $return_res['code'] = '1005';
                $return_res['results'] = "user error";
                $return_res['msg'] = "user_id错误，用户不存在，请重新登陆。";
                echo json_encode($return_res);
                Yii::$app->end();
            }

            $user_id = $UserBaseInfo->user_id;
            $page = $this->postData['page']+0;
            $pagesize = $this->postData['pagesize']+0;

            $pagestart = 0;
            if ($page > 0){
                $pagestart = ($page - 1) * $pagesize;
            }
            $connect = Yii::$app->db;
            $command = $connect->createCommand("select create_time,money,status,phone from tixian_bills where user_id=:user_id order by create_time desc limit ".$pagestart.",".$pagesize);
            $command->bindParam(':user_id',$user_id);
            $return_res = array();
            try
            {
                $dataReader = $command->query();
                $return_res['code'] = "0";
                $return_res['rows'] = $dataReader->count();
                $return_res['results'] = $dataReader->readALL();
            }
            catch (Exception $e)
            {
                $return_res['code'] = '1005';
                $return_res['results'] = "query error";
                $return_res['msg'] = "获取数据失败。";
            }
            echo json_encode($return_res);
        }else
        {
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }

    /*
   * 公告管理
   * @param user_id:user_id
   */
    public function actionNoticeData()
    {
        if(isset($this->postData["user_id"]))
        {   $user_id = $this->postData['user_id'];
            if(!UserBaseInfo::find()->where('user_id=:user_id',[':user_id'=>$user_id])->exists())
            {
                $return_res['code'] = '1005';
                $return_res['results'] = "user error";
                $return_res['msg'] = "用户不存在，请重新登陆。";
                echo json_encode($return_res);
                Yii::$app->end();
            }
            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] = "";

            echo json_encode($return_res);
        }else{
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] = "缺少参数。";
            echo json_encode($return_res);
        }
    }
    /*
  * WX AB营销人数总监
  * @param user_id:user_id
  */
    public  function actionAddWx(){
        if (isset($this->postData['user_id'])){
            $user_id = $this->postData['user_id'];
            $Counta=UserBaseInfo::find()->select(['a_from'])->where(['a_from'=>$user_id])->count();
            $Paya=UserBaseInfo::find()->select(['a_from'])->where(['a_from'=>$user_id,'role_type'=>1])->count();
            $Countb=UserBaseInfo::find()->select(['b_from'])->where(['b_from'=>$user_id])->count();
            $Payb=UserBaseInfo::find()->select(['b_from'])->where(['b_from'=>$user_id,'role_type'=>1])->count();
            $wx=UserBaseInfo::find()->select(['wx'])->where(['user_id'=>$user_id])->one();

            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] =[
                'wx'=>$wx['wx'],
                'Counta'=>$Counta,
                'Paya'=>$Paya,
                'Countb'=>$Countb,
                'Payb'=>$Payb,
            ];
            echo json_encode($return_res);
        }else{
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] ='缺少参数';
            echo json_encode($return_res);
        }
    }
    /*
* WX
* @param user_id:user_id
* @param wx:wx
*/
    public  function actionWx(){
        if (isset($this->postData['user_id'])&&isset($this->postData['wx'])){
            $user_id = $this->postData['user_id'];

            $wixin = $this->postData['wx'];
            $UserBaseInfo = UserBaseInfo::find()->select(['id','user_id'])->where("user_id=:user_id",[":user_id"=>$user_id])->one();


           if (empty($UserBaseInfo['wx'])){
               $return_res['code'] = '1005';
               $return_res['results'] = "user error";
               $return_res['msg'] = "用户不存在，请重新输入";
           }

            $UserBaseInfo->wx =$wixin;
            if(!$UserBaseInfo ->save())
            {
                foreach($UserBaseInfo->errors as $key=>$value)
                {
                    $return_msg = $value[0];
                    break;
                }
                $return_res['code'] = '1006';
                $return_res['results'] = "insert wx error";
                $return_res['msg'] = $return_msg;
                echo json_encode($return_res);
                Yii::$app->end();
            }

            $return_res['code'] = '0';
            $return_res['results'] = "success";
            $return_res['msg'] =[
              'wx'=>$UserBaseInfo->wx,
            ];
            echo json_encode($return_res);
        }else{
            $return_res['code'] = '1004';
            $return_res['results'] = "missing params";
            $return_res['msg'] ='缺少参数';
            echo json_encode($return_res);
        }
    }

}
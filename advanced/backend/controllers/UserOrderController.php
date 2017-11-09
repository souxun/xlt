<?php

namespace backend\controllers;

use frontend\models\UserAddress;
use Yii;
use backend\models\UserOrder;
use backend\models\UserOrderSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * UserOrderController implements the CRUD actions for UserOrder model.
 */
class UserOrderController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all UserOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserOrder model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new UserOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserOrder();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the UserOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserOrder::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    //发货弹框
    public function actionDeliverHtml($id)
    {
        //$this->layout = false;
        $model = $this->findModel($id);
        return $this->renderAjax('deliver-html',[
                "model"=>$model,
                "expressCompany"=>Yii::$app->params['expressCompany'],
            ]
        );
    }

    //导出excel
    public function actionLoadExcel()
    {
//        $query = UserOrder::find()->with('address');
//        $query->select(['user_id','out_trade_no','order_money','num','status','create_time','trade_no','pay_time']);/*

       $query ="select u.user_id,u.out_trade_no,u.order_money,u.num,u.status,u.create_time,u.trade_no,u.pay_time,a.name,a.phone,a.province,a.city,a.country,a.detail_address from  user_order as u  left  join people_user_address as a on u.user_id = a.user_id";
     /*   if(!empty(Yii::$app->request->get('UserOrderSearch')))
        {
            $data = Yii::$app->request->get('UserOrderSearch');

            $query->andFilterWhere([
                'user_id' => $data['user_id'],
                'status' => $data['status'],
            ]);

            $query->andFilterWhere(['like', 'out_trade_no', $data['out_trade_no']]);
        }*/
        $connection  = Yii::$app->db;
        $command = $connection->createCommand($query);
        $list = $command->queryAll();

//        $list = $query->asArray()->all();

        if(empty($list))
        {
            echo 'no data';
            Yii::$app->end();
        }
        $statusArr = ['未支付','待发货','已发货','已收货','待补单'];
//        $typeArr = [1=>'微信',2=>'支付宝'];
        $typeArr ='支付宝';

        require(__DIR__ .'/../../common/helps/excel/PHPExcel.php');
        require(__DIR__ .'/../../common/helps/excel/PHPExcel/Writer/Excel2007.php');
        $objPHPExcel = new \PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        //添加头部

        $header = ['用户ID','订单号','订单金额','商品数量','订单状态','下单时间','微信交易号','支付时间','收货人','手机号','省份','市区','地区','详细地址'];
        $hk = 0;
        foreach ($header as $k => $v)
        {
            $colum = \PHPExcel_Cell::stringFromColumnIndex($hk);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($colum."1", $v);
            $hk += 1;

        }
        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();
        foreach($list as $key => $rows)  //行写入
        {
            $span = 0;
            foreach($rows as $keyName => $value) // 列写入
            {
                switch($keyName)
                {
                    case 'status':
                        $value = $statusArr[$value];

                        break;
                }
                $j = \PHPExcel_Cell::stringFromColumnIndex($span);

                $objActSheet->setCellValue($j.$column, $value);

                $span++;
            }
            $column++;
        }
        // Rename sheet
        //$objPHPExcel->getActiveSheet()->setTitle($title);
        // Save Excel 2007 file
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPExcel);

        $filename = date("YmdHis");
        header("Pragma:public");
        header("Content-Type:application/x-msexecl;name=\"{$filename}.xls\"");
        header("Content-Disposition:inline;filename=\"{$filename}.xls\"");
        $objWriter->save("php://output");

    }
    //发货处理
    public function actionDeliver()
    {
        if(Yii::$app->request->isPost)
        {
            $data = Yii::$app->request->post();
            $id = $data['order_id'];
            $deliver_time = $data['deliver_time'];
            $express_company = $data['express_company'];
            $express_number = $data['express_number'];
            //查询订单是否存在
            $orderInfo = UserOrder::find()->select(['user_id'])->where("id=:id and status=4",[':id'=>$id])->one();
            if(empty($orderInfo))
            {
                return json_encode(array("status"=>"error","msg"=>"该订单不存在！"));
                Yii::$app->end();
            }
            $upArr = array(
                "status"=>2,
                "deliver_time"=>$deliver_time,
                "express_name"=>$express_company,
                "express_num"=>$express_number,
            );
            $result=UserOrder::updateAll($upArr,"id=:id and status=4",array(":id"=>$id));
            if($result!==0){
                return json_encode(["status"=>"success","msg"=>"发货成功！"]);
            }
            return json_encode(["status"=>"error","msg"=>"发货失败，未知错误"]);
        }
    }

    //标记发货处理
    public function actionMakeOrder(){
        $get=Yii::$app->request->get();
        $id=$get['id'];
        $orderInfo = UserOrder::find()->select(['user_id'])->where("id=:id and status=1",[':id'=>$id])->one();
        $session = Yii::$app->session;
        Yii::$app->user->setReturnUrl(Yii::$app->request->referrer);

        if(empty($orderInfo))
        {
            $session->setFlash('info', '设置代补单失败，该订单不存在');

           return $this->goBack();
        }
        $upArr = array(
            "status"=>4,
        );
        UserOrder::updateAll($upArr,"id=:id and status=1",array(":id"=>$id));
        $session->setFlash('info', '设置代补单成功');
        return $this->goBack();
    }




}
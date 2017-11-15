<?php

namespace backend\controllers;

use Yii;
use frontend\models\UserBaseInfo;
use backend\models\UserBaseInfoSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserBaseInfoController implements the CRUD actions for UserBaseInfo model.
 */
class UserBaseInfoController extends Controller
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
     * Lists all UserBaseInfo models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserBaseInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserBaseInfo model.
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
     * Creates a new UserBaseInfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new UserBaseInfo();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->renderAjax('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing UserBaseInfo model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
            return $this->redirect(['index']);
        } else {
            return $this->renderAjax('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing UserBaseInfo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    //导出excel
    public function actionLoadExcel()
    {
//        $query = UserOrder::find()->with('address');
//        $query->select(['user_id','out_trade_no','order_money','num','status','create_time','trade_no','pay_time']);/*

        $query ="select u.user_id,u.wx,u.nickname,u.sex,u.role_type,u.a_from,u.b_from,a.name,a.phone,a.province,a.city,a.country,a.detail_address,u.create_time from  user_base_info as u  left  join people_user_address as a on u.user_id = a.user_id order by u.create_time desc  ";

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

        $statusArr = ['非会员','会员'];
//        $sexArr = [1=>'男',2=>'女'];



        require(__DIR__ .'/../../common/helps/excel/PHPExcel.php');
        require(__DIR__ .'/../../common/helps/excel/PHPExcel/Writer/Excel2007.php');
        $objPHPExcel = new \PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        //添加头部

        $header = ['用户ID','微信','昵称','性别','支付状态','一级推荐人','二级推荐人','收货人','手机号','省份','市区','地区','详细地址','创建时间'];
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

//                   case 'sex':
//                        $value = $sexArr[$value];
//                        break;
                    case 'role_type':
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


    /**
     * Finds the UserBaseInfo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserBaseInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserBaseInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionTop100()
    {
        $searchModel = new UserBaseInfoSearch();
        $dataProvider = $searchModel->searchs(Yii::$app->request->queryParams);
//    print_r($dataProvider);die;
        return $this->render('top100', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}

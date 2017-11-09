<?php

namespace backend\controllers;

use frontend\models\UserBaseInfo;
use Yii;
use frontend\models\TixianBills;
use frontend\models\RefreeSettleBill;
use backend\models\TixianBillsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TixianBillsController implements the CRUD actions for TixianBills model.
 */
class TixianBillsController extends Controller
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
     * Lists all TixianBills models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TixianBillsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    //确认打款
    public function actionConfirm()
    {
        if(Yii::$app->request->isPost)
        {
            $bill_id = Yii::$app->request->post('bill_id');
            $model = $this->findModel($bill_id);
            $model->status = 1;
            $model->save(true,['status']);
            echo json_encode(["status"=>"success","msg"=>"确认成功。"]);
        }
    }
    //导出excel
    public function actionLoadExcel()
    {
        $query = TixianBills::find();
        $query->select(['user_id','nickname','money','phone','type','status','account','create_time']);

        if(!empty(Yii::$app->request->get('TixianBillsSearch')))
        {
            $data = Yii::$app->request->get('TixianBillsSearch');

            $query->andFilterWhere([
                'user_id' => $data['user_id'],
                'status' => $data['status'],
            ]);

            $query->andFilterWhere(['like', 'nickname', $data['nickname']]);
        }

        $list = $query->asArray()->all();

        if(empty($list))
        {
            echo 'no data';
            Yii::$app->end();
        }
        $statusArr = ['未确认','已确认'];
//        $typeArr = [1=>'微信',2=>'支付宝'];
        $typeArr ='支付宝';

        require(__DIR__ .'/../../common/helps/excel/PHPExcel.php');
        require(__DIR__ .'/../../common/helps/excel/PHPExcel/Writer/Excel2007.php');
        $objPHPExcel = new \PHPExcel();

        // Add some data
        $objPHPExcel->setActiveSheetIndex(0);
        //添加头部
        $header = ['用户ID','姓名','提现金额','手机号','类型','状态','账号','创建时间'];
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
                    case 'type':
                        $value = $typeArr[$value];
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
     * Displays a single TixianBills model.
     * @param integer $id
     * @return mixed
     */
//    public function actionView($id)
//    {
//
////        $model = $this->findModel($id);
//
//        return $this->render('view', [
//            'model' => $this->findModel($id),
//        ]);
//    }

    /**
     * Creates a new TixianBills model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
//    public function actionCreate()
//    {
//        $model = new TixianBills();
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('create', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Updates an existing TixianBills model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionUpdate($id)
//    {
//        $model = $this->findModel($id);
//
//        if ($model->load(Yii::$app->request->post()) && $model->save()) {
//            return $this->redirect(['view', 'id' => $model->id]);
//        } else {
//            return $this->render('update', [
//                'model' => $model,
//            ]);
//        }
//    }

    /**
     * Deletes an existing TixianBills model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
//    public function actionDelete($id)
//    {
//        $this->findModel($id)->delete();
//
//        return $this->redirect(['index']);
//    }

    /**
     * Finds the TixianBills model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TixianBills the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TixianBills::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

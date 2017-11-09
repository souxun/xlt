<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TixianBillsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tixian-bills-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= Html::button('导出EXCEL', ['id'=>'load_excel','class' => 'btn btn-success']) ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'user_id',
            'nickname',
            [
                'attribute'=>'moeny',
                'value'=>function($model){
                    return $model->money;
                }
            ],
            [
                'attribute'=>'type',
                'value'=>function($model){
                    return $model->type == 1 ? "支付宝" :  "无";
                },
                'filter'=>[
                    '1'=>'支付宝',

                ],
            ],
            [
                'attribute'=>'status',
                'value'=>function($model){
                    return $model->status == 0 ? "未确认" : "已确认";
                },
                'filter'=>[
                    "0"=>"未确认",
                    "1"=>"已确认",
                ]
            ],
            [
                'attribute'=>'phone',
                'filter'=>false,
            ],

            'account',
            [
                'attribute'=>'create_time',
                'filter'=>false,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{confirm}',
                'buttons' => [
                    'confirm' => function($url,$model){
                        if($model->status == 0)
                        {
                            return 	Html::a('确认', 'javascript:;',[
                                "class"=>"confirm",
                                "bill_id"=>$model->id,
                            ]);
                        }
                        return null;
                    }
                ],
            ],


            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
<?php $this->beginBlock('confirm') ?>
$(function($) {
$(".confirm").on('click',function(){
var bill_id = $(this).attr("bill_id");
var result = confirm('请确保该提现已经打款！');
if(result == true)
{
$.post('<?php echo Url::toRoute('confirm');?>',{'bill_id':bill_id},function(data){
if(data.status == 'success')
{
alert(data.msg);
setTimeout(function(){
location.reload();
},1000);
}else
{
alert(data.msg);
}
},'json')
}
})

$("#load_excel").on('click',function(){
var href = location.href;
var ih = href.indexOf("?");
var search = '';
if(ih != -1)
{
var search = href.substr(ih+1);
}
var result = confirm('确定要导入到excel吗？');
if(result == true)
{
location.href = '<?php echo Url::toRoute('load-excel');?>?'+search;
}
})
});
<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['confirm'], \yii\web\View::POS_END); ?>


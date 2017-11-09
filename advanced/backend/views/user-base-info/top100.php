<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserBaseInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '直推A级营销总监Top100';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-base-info-index table-responsive">
    <table class="table">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= Html::button('导出EXCEL', ['id'=>'load_excel','class' => 'btn btn-success']) ?>&nbsp;
        <?= Html::a('直推A级营销总监Top100', ['user-base-info/index'], ['class' => 'btn btn-success']) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

             [
                 'attribute'=>'a_from',
                'label'=>'用户ID',

                 ],

           [
               'label'=>'数量',
               'value'=>function($date){
                     return $date->count;
               }
           ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </table>
</div>
<?php $this->beginBlock('confirm') ?>
    $(function($) {

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
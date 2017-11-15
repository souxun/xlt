<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use \yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserBaseInfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-base-info-index table-responsive">
    <table class="table">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= Html::button('导出EXCEL', ['id'=>'load_excel','class' => 'btn btn-success']) ?>&nbsp;
        <?= Html::a('直推A级营销总监Top100', ['user-base-info/top100'], ['class' => 'btn btn-success']) ?>
        <?=
        Html::a('创建栏目', ['create'], [
            'class' => 'btn btn-success',
            'id' => 'create', // 按钮的id随意
            'data-toggle' => 'modal', // 固定写法
            'data-target' => '#operate-modal', // 等于modal begin中设定的参数id值
        ])
        ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
//            'id',

            'user_id',
            'wx',
            'nickname',
            [
                'attribute'=>'sex',
                'value'=>function($model){
                    return $model->sex == 1 ? "男" : ($model->sex == 2 ? "女" : "未知");
                },
                'filter'=>["1"=>"男","2"=>"女"],
            ],
             [
                 'attribute'=>'province',
                 'value'=>function($model){
                    return $model->provinces['province'];
                 },
             ],
            [
                'attribute'=>'city',
                'value'=>function($model){
                    return $model->provinces['city'];
                },
            ],
            [
                'label'=>'详细地址',
                'value'=>function($model){
                    return $model->provinces['province'].$model->provinces['city'].$model->provinces['country'].$model->provinces['detail_address'];
                },
            ],
           [
                'attribute'=>'role_type',
               'label' => '支付状态',
                'value'=>function($model){
                    return $model->role_type == 1 ? "会员"  : "非会员";
                },
               'filter'=>["1"=>"会员","2"=>"非会员"],
            ],
             'a_from',
             'b_from',
            'create_time',
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </table>
</div>
<?php
Modal::begin([
    'id' => 'operate-modal',
    'header' => '<h4 class="modal-title"></h4>',
]);
Modal::end();
?>
<?php

// 异步请求的地址
$requestCreateUrl = Url::toRoute('create');
$js = <<<JS
// 创建操作
$('#create').on('click', function () {
    $('.modal-title').html('创建栏目');
    $.get('{$requestCreateUrl}',
        function (data) {    
            // 弹窗的主题渲染页面
            $('.modal-body').html(data);
        }  
    );
});
JS;
$this->registerJs($js);
?>
<?php
// 更新
$requestUpdateUrl = Url::toRoute('update');
$js = <<<JS
    // 更新操作
    $('.btn-update').on('click', function () {
        $('.modal-title').html('栏目信息');
        $.get('{$requestUpdateUrl}', { id: $(this).closest('tr').data('key') },
            function (data) {
                $('.modal-body').html(data);
            }  
        );
    });
JS;
$this->registerJs($js);
?>
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
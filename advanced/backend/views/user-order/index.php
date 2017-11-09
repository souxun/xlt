<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use \yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户订单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-order-index,table-responsive">
    <table class="table">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
     <?= Html::button('导出EXCEL', ['id'=>'load_excel','class' => 'btn btn-success']) ?>

    <p>
        <?php // Html::a('Create User Order', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//            'id',
            'user_id',
            'out_trade_no',
            'order_money',
            'num',
//             'fee_money',
            [
                'attribute'=>'status',
                'value'=>function($model){
                return $model->status == 0 ? "待付款" : ($model->status == 1 ? "待发货" : ($model->status == 2 ? "待收货" : ($model->status==4?'待补单':'已完成')));
                },
                'filter'=>false,
            ],
             'create_time',
             'trade_no',
             'pay_time',
            [
                'label'=>'收货人',
                'value'=>function($data){
                    return $data->address['name'];
                },
            ],
            [
                'label'=>'手机号',
                'value'=>function($data){
                    return $data->address['phone'];
                },
            ],
            [
                'label'=>'收货地址',
                'value'=>function($data){
                    return $data->address['province'].$data->address['city'].$data->address['country'].$data->address['detail_address'];
                },
            ],
//             'deliver_time',
//             'express_name',
//             'express_num',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}&nbsp;&nbsp;{deliver}&nbsp;&nbsp;{makeorder}',
                'headerOptions' => ['style' => 'width:8%'],
                'buttons' => [
                    'deliver' => function($url,$model){
                        if($model->status == 4)
                        {
                            return 	Html::a('发货', '#', [
                                'data-toggle' => 'modal',
                                'data-target' => '#deliver-modal',
                                'class' => 'data-deliver',
                            ]);
                        }
                        return null;
                    },
                    'makeorder'=>function($url,$model,$key){
                        if($model->status == 1)
                        {
                            $url=Url::to(['make-order','id'=>$model->id]);
                            return 	Html::a('标记补单', $url, [
//                                'data-toggle' => 'modal',
//                                'data-target' => '#deliver-modal',
//                                'class' => 'make-order',
                            ]);
                        }
                        return null;
                    },
                ],
            ],
        ],
    ]); ?>
    </table>
</div>
<?php
Modal::begin([
'id' => 'deliver-modal',
'header' => '<h4 class="modal-title">发货</h4>',
'footer' => '<a href="javascrip:;" class="btn btn-primary deliver">确定</a>&nbsp;<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
]);
$requestUrl = Url::toRoute('deliver-html');
$js = <<<JS
$('.data-deliver').on('click', function () {
	$.get('{$requestUrl}', { id: $(this).closest('tr').data('key') },
	function (data) {
		$('#deliver-modal .modal-body').html(data);
	} 
	);
});
JS;
$this->registerJs($js);
Modal::end();

?>
<?php $this->beginBlock('deliver') ?>
    $(function($) {
		$(".deliver").on('click',function(){
			var order_id = $("#order_id").val();
			var deliver_time = $.trim($("#deliver_time").val());
			if(deliver_time == '')
			{
				alert('请选择发货时间！');
				return false;
			}
			var phone = $.trim($("#phone").val());
			var address = $.trim($("#address").val());
			var express_code = $("#express_company").val();
			if(express_code == '0')
			{
				alert('请选择快递公司！');
				return false;
			}
			var express_company = $("#express_company option:selected").html();
			var express_number = $("#express_number").val();
			if(express_number == '')
			{
				alert('请输入快递单号！');
				return false;
			}

			$.post('<?php echo Url::toRoute('deliver');?>',{'order_id':order_id,'deliver_time':deliver_time,'express_company':express_company,'express_number':express_number},function(data){
				if(data.status == 'success')
				{
					alert(data.msg);
					setTimeout(function(){
						location.reload();
					},1500);
				}else
				{
					alert(data.msg);
				}
			},'json')
		})


		$(".order").on('click',function(){
			var user_id = $.trim($("#user_id").val());
			var product_id = $.trim($("#product_id").val());
			var product_num = $.trim($("#product_num").val());
			var money = $("#money").val();
			var address_id = $("#address_id").val();
			var one_from = $("#one_from").val();

			$.post('<?php echo Url::toRoute('order');?>',{'user_id':user_id,'product_id':product_id,'product_num':product_num,'money':money,'address_id':address_id,'one_from':one_from},function(data){
				if(data.status == 'success')
				{
					alert(data.msg);
					setTimeout(function(){
						location.reload();
					},1500);
				}else
				{
					alert(data.msg);
				}
			},'json')
		})
    });
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
<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['deliver'], \yii\web\View::POS_END); ?>

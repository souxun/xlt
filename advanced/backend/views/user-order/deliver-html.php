<?php
use yii\helpers\Html;
use \dosamigos\datetimepicker\DateTimePicker;
use frontend\assets\AppAsset;

?>
<link rel="stylesheet" type="text/css" href="jquery.datetimepicker.css"/ >
<script src="jquery.js"></script>
<script src="jquery.datetimepicker.js"></script>

<div style="margin-left:20%;">
	<input type="hidden" id="order_id" value="<?php echo $model->id;?>">
	<div style="height:50px;">
		<label style="width:25%">请选择发货时间</label>
		<style type="text/css">
		.form-control{width:40%;display:inline;}
		</style>
        <input id="deliver_time" name="deliver_time" type="text" value="<?php echo date("Y-m-d H:i:s"); ?>">
<?php //echo DateTimePicker::widget([
//'          id' => 'deliver_time',
//			'name' => 'deliver_time',
//			'language' => 'zh-CN',
//			'value' => date("Y-m-d H:i:s"),
//			'template' => '{input}',
//			'clientOptions' => [
//          'autoclose' => true,
//          'format' => 'yyyy-mm-dd hh:ii:ss'
//            ]
//		]);
        ?>
	</div>
	<div style="height:50px;">
		<label style="width:25%">收货人手机号</label>
		<input type="text" id="phone" value="<?php echo empty($model->address->phone)?'':$model->address->phone;?>"></div>
	<div style="height:50px;">
		<label style="width:25%">收货地址</label>
		<input type="text" id="address" value="<?php
        echo $model->address['province'].$model->address['city'].$model->address['country'].$model->address['detail_address'];
        ?>" style="width:60%">
	</div>
	<div style="height:50px;">
		<label style="width:25%">请选择快递公司</label>
		<select id="express_company">
		<?php
		foreach($expressCompany as $k=>$v)
		{
			echo '<option value="'.$k.'" '.($k == 'kuaijiesudi' ? "selected" : "").'>'.$v.'</option>';
		}
		?>
		</select>
	</div>
	<div style="height:50px;">
		<label style="width:25%">快递单号</label>
		<input type="text" id="express_number" value="">
	</div>
</div>

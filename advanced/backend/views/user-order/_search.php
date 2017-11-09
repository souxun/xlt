<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DateRangePicker;

//print_r(Yii::$app->request->get())

/* @var $this yii\web\View */
/* @var $model backend\models\UserOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'out_trade_no') ?>

    <?= $form->field($model, 'order_money') ?>

    <?php // echo $form->field($model, 'fee_money') ?>

    <?php  echo $form->field($model, 'status')->dropDownList([""=>"请选择","0"=>"未付款","1"=>"待发货","2"=>"待收货","3"=>"已完成",4=>'待补单']) ?>


    <?php  echo $form->field($model, 'trade_no') ?>

    <?= $form->field($model, 'start_time')->widget(
        DateRangePicker::className(), [
        'attributeTo' => 'end_time',
        'form' => $form, // best for correct client validation
        'language' => 'zh-CN',
        'size' => 'lg',
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd'
        ]
    ]);?>

    <?php  //echo $form->field($model, 'deliver_time') ?>

    <?php  //echo $form->field($model, 'express_name') ?>

    <?php //echo $form->field($model, 'express_num') ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

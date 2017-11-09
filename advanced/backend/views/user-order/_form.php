<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-order-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'out_trade_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'num')->textInput() ?>

    <?= $form->field($model, 'fee_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'trade_no')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pay_time')->textInput() ?>

    <?= $form->field($model, 'deliver_time')->textInput() ?>

    <?= $form->field($model, 'express_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'express_num')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

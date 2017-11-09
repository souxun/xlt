<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\TixianBills */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tixian-bills-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

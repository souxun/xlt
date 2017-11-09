<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TixianBillsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tixian-bills-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'nickname') ?>

    <?= $form->field($model, 'money') ?>

    <?= $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'account') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

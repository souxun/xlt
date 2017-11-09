<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserBaseInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-base-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'openid') ?>

    <?= $form->field($model, 'nickname') ?>

    <?= $form->field($model, 'sex') ?>

    <?php // echo $form->field($model, 'province') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'role_type') ?>

    <?php // echo $form->field($model, 'a_from') ?>

    <?php // echo $form->field($model, 'b_from') ?>

    <?php // echo $form->field($model, 'session_id') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

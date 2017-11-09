<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserBaseInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-base-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'openid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sex')->textInput() ?>

    <?= $form->field($model, 'province')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <?= $form->field($model, 'role_type')->textInput() ?>

    <?= $form->field($model, 'a_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'b_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'session_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

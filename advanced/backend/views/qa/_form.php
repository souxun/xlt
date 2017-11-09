<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Qa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'question')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'answer')->textarea(['rows' => 3]) ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

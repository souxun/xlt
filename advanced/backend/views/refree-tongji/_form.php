<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\RefreeTongji */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="refree-tongji-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subNum')->textInput() ?>

    <?= $form->field($model, 'payNum')->textInput() ?>

    <?= $form->field($model, 'create_time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

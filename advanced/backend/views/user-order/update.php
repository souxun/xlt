<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserOrder */

$this->title = 'Update User Order: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

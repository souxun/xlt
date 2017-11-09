<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserBaseInfo */

$this->title = 'Update User Base Info: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'User Base Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-base-info-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

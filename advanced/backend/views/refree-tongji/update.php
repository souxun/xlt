<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\models\RefreeTongji */

$this->title = 'Update Refree Tongji: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Refree Tongjis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="refree-tongji-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\RefreeTongji */

$this->title = 'Create Refree Tongji';
$this->params['breadcrumbs'][] = ['label' => 'Refree Tongjis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="refree-tongji-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

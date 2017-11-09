<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Qa */

$this->title = 'Create Qa';
$this->params['breadcrumbs'][] = ['label' => 'Qas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

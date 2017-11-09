<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\TixianBills */

$this->title = 'Create Tixian Bills';
$this->params['breadcrumbs'][] = ['label' => 'Tixian Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tixian-bills-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

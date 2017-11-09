<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserOrder */

$this->title = 'Create User Order';
$this->params['breadcrumbs'][] = ['label' => 'User Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

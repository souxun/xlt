<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model frontend\models\UserBaseInfo */

$this->title = 'Create User Base Info';
$this->params['breadcrumbs'][] = ['label' => 'User Base Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-base-info-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

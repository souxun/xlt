<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserAddress */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'User Addresses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-address-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'user_id',
            'name',
            'phone',
            'fix_tel',
            'zip_code',
            'province',
            'city',
            'country',
            'detail_address',
            'create_time',
        ],
    ]) ?>

</div>

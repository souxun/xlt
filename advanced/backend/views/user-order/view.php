<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\UserOrder */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-update']) ?>
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
            'out_trade_no',
            'order_money',
            'num',
            'fee_money',
            'status',
            'create_time',
            'trade_no',
            'pay_time',
            'deliver_time',
            'express_name',
            'express_num',
        ],
    ]) ?>

</div>

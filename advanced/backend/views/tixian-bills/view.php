<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\TixianBills */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tixian Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tixian-bills-view">

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
            'nickname',
            "money",
            [
                'attribute'=>'type',
                'value'=>function($model){
                    return $model->type == 1 ? "支付宝" : "无";
                },
            ],
            'account',
            [
                'attribute'=>'status',
                'value'=>function($model){
                    return $model->status == 0 ? "未确认" : "已确认";
                },
            ],
            'phone',
            'create_time',
        ],
    ]) ?>

</div>

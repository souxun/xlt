<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\UserBaseInfo */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'User Base Infos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-base-info-view">

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
            'openid',
            'nickname',
            'sex',
            'province',
            'city',
            'create_time',
            'role_type',
            'a_from',
            'b_from',
            'session_id',
            'name',
            'phone',
        ],
    ]) ?>

</div>

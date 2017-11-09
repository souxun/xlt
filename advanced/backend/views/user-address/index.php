<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserAddressSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Addresses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-address-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create User Address', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'user_id',
            'name',
            'phone',
            'fix_tel',
            // 'zip_code',
             'province',
             'city',
             'country',
             'detail_address',
            // 'create_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

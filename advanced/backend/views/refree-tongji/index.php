<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RefreeTongjiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '统计直推A级营销总监Top100';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="refree-tongji-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'user_id',
            [
                'attribute'=>'subNum',
                'label' => '总关注量',

            ],
            [
                'attribute'=>'payNum',
                'label' => '总付费量',

            ],
            'create_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>

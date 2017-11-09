<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\QaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '百问百答';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qa-index table-responsive " >
    <table class="table">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增百问百答', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'question',
            'answer:ntext',
            'create_time',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </table>
</div>

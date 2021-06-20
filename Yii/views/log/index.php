<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index ">

    <h1><?= Html::encode($this->title) ?></h1>

    <!--    <p>-->
    <!--        Html::a('Create Message', ['create'], ['class' => 'btn btn-success']) -->
    <!--    </p>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                [
                    'attribute' => 'level_id',
                    'filter' => app\models\Level::find()->select(['key', 'id'])->indexBy('id')->column(),
                    'value' => 'level.key',
                ],
                [
                    'attribute' => 'message',
                    'value'     => function ($data) {
                        return \app\models\Helper\Helper::displayOnGrid($data['message']);
                    },
                ],
                'create',
                'ip',

                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                ],
            ],
        ]
    ); ?>


</div>

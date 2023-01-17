<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\JsMessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Js Error Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index ">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'filterModel'  => $searchModel,
            'columns'      => [
                ['class' => 'yii\grid\SerialColumn'],
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
                'user_id',
                [
                    'attribute' => 'user_agent',
                    'value'     => function ($data) {
                        return \app\models\Helper\Helper::displayOnGrid($data['user_agent']);
                    },
                ],
                [
                    'attribute' => 'url',
                    'value'     => function ($data) {
                        return \app\models\Helper\Helper::displayOnGrid($data['url'], 32);
                    },
                ],
                'create',
                'ip',
                [
                    'class'    => 'yii\grid\ActionColumn',
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', '/log/jsview?id=' . $model->id, [
                                'title' => Yii::t('app', 'lead-view'),
                            ]);
                        },
                        ]
                ],
            ],
        ]
    ); ?>


</div>

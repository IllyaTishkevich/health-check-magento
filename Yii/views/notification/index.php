<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Notification';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="level-notification-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Notification', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            [
                'attribute' => 'level_id',
                'filter' => app\models\Level::find()->select(['key', 'id'])->indexBy('id')->column(),
                'value' => 'level.key',
            ],
            [
                'attribute' => 'notification_id',
                'filter' => app\models\Notification::find()->select(['name', 'id'])->indexBy('id')->column(),
                'value' => 'notification.name',
            ],
            'settings:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

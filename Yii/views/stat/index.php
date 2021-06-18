<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Messages';

$this->params['breadcrumbs'][] = $this->title;

$levels = [];

foreach (\app\models\Level::find()->all() as $ll)
{
    $levels[$ll->id] = $ll->key;
}

$stat   = new app\models\StatForm();
$stat->load(Yii::$app->request->post());
?>

<h1><?= Html::encode($this->title) ?></h1>


<div class="message-form">

    <?php $form   = \yii\widgets\ActiveForm::begin(); ?>

    <?= $form->field($stat, 'logLevel')->dropDownList($levels) ?>



    <?= $form->field($stat, 'fromTime')->textInput() ?>
    <?= $form->field($stat, 'toTime')->textInput() ?>



    <div class="form-group">
        <?= Html::submitButton('Show Count', ['class' => 'btn btn-success']) ?>
    </div>

    <?php \yii\widgets\ActiveForm::end(); ?>

    <?php
    $ms = \app\models\Message::find()
        ->where(['level_id' => $stat->logLevel]);

    $filter = array(
        'level_id' => $stat->logLevel,
    );

    if ($stat->fromTime)
    {
        $ms->andWhere('`create` >= :f', [':f' => $stat->fromTime]);
    }

    if ($stat->toTime)
    {
        $ms->andWhere('`create` <= :t', [':t' => $stat->toTime]);
    }

    $count = $ms->count();

    ?>

    <div class="stat-index">
        <h2>
            Count: <?= $count ?>
        </h2>
    </div>


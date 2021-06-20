<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\LevelNotification */

$this->title = 'Create Level Notification';
$this->params['breadcrumbs'][] = ['label' => 'Notifications', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="level-notification-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

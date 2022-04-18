<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\LevelNotification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="level-notification-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'level_id')
        ->label('Level')
        ->dropDownList($model->getDropDownLevel()); ?>

    <?= $form->field($model, 'notification_id')
        ->label('Notification')
        ->dropDownList($model->getDropDownNotification()); ?>

    <?= $form->field($model, 'settings')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

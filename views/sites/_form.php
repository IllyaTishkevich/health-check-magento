<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sites */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sites-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'site_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'available')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cron_status')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
use yii\widgets\ActiveForm;
/* @var $model \app\models\UploadForm */
?>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<div class="input-group">
    <span class="input-group-btn">
        <?= $form->field($model, 'file',['options' => ['class' => 'form-control']])->fileInput() ?>
    </span>
    <span class="input-group-btn">
        <input type="submit" name="submit" value="Upload" class="btn btn-success"/>
    </span>
</div>

<?php ActiveForm::end() ?>



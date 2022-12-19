<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sites */

$this->title = 'Create Sites';
$this->params['breadcrumbs'][] = ['label' => 'Sites', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sites-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Project */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <ul class="nav nav-tabs">
        <li role="presentation" class="active"><a href="#">General</a></li>
        <li role="presentation"><a href="#">Natification</a></li>
    </ul>
    <div class="project-view-content">
        <p class="project-delete-button">
            <?php /** echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) **/?>
            <?php  echo  Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ])  ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                'auth_key',
                'url:url',
            ],
        ]) ?>
    </div>

</div>

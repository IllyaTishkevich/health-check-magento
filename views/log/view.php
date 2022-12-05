<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Message */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <?php
        $messageJson = json_decode($model->message);
    ?>
    <table id="w0" class="table table-striped table-bordered detail-view">
        <tbody>
            <tr>
                <th>ID</th>
                <td><?= $model->id; ?></td>
            </tr>
            <tr>
                <th>Message</th>
                <td>
                    <?= $this->context->messageParser($messageJson); ?>
                </td>
            </tr>
            <tr>
                <th>Create</th>
                <td><?= $model->create; ?></td>
            </tr>
            <tr>
                <th>Ip</th>
                <td><?= $model->ip; ?></td>
            </tr>
        </tbody>
    </table>

</div>

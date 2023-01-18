<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\JsMessage */

$this->title = 'Error:' . $model->message;
$this->params['breadcrumbs'][] = ['label' => 'Messages', 'url' => ['js']];
$this->params['breadcrumbs'][] = 'Error:' . $model->message;
\yii\web\YiiAsset::register($this);
?>
<div class="message-view">

    <h1><?= Html::encode('Error:' . $model->message) ?></h1>

    <?php
        $messageJson = $this->context->collectEvents(json_decode($model->events));
    ?>
    <div class="error-message-data">
        <table class="table table-striped table-bordered detail-view">
            <thead>
                <tr>
                    <th>Events Trace</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <?= $this->context->eventsParser($messageJson); ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="table table-striped table-bordered detail-view">
            <tbody>
                <tr>
                    <th>Error Message</th>
                    <td><?= $model->message; ?></td>
                </tr>
                <tr>
                    <th>Error Trace</th>
                    <td><?= $model->trace; ?></td>
                </tr>
                <tr>
                    <th>User Agent</th>
                    <td><?= $model->user_agent; ?></td>
                </tr>
                <tr>
                    <th>User unique Id</th>
                    <td><?= $model->user_id; ?></td>
                </tr>
                <tr>
                    <th>Created</th>
                    <td><?= $model->create; ?></td>
                </tr>
                <tr>
                    <th>Ip</th>
                    <td><?= $model->ip; ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

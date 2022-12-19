<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SitesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $upload app\models\UploadForm */

$this->title = 'Mass .git checker';
$this->params['breadcrumbs'][] = ['label' => 'Tools', 'url' => ['/tools/list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sites-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-lg-12 breadcrumb">
        <div class="col-lg-1">
            <?= Html::a('Add Sites', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div class="col-lg-4">
            <?= $this->render('_upload', [
                'model' => $upload,
            ]) ?>
        </div>
    </div>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'site_url:url',
            'status_code',
            'available',
            'cron_status',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>

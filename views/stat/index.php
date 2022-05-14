<?php

use yii\helpers\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ProjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Statistics';

$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('/js/stat/app.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]);
?>

<div id="app-container">
</div>
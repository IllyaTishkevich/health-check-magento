<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Project */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['view']];
$this->params['breadcrumbs'][] = $this->title;
$this->registerJsFile('/js/stat/app.js',
    ['depends' => [\yii\web\JqueryAsset::className()]]);

\yii\web\YiiAsset::register($this);
?>
<div class="project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div id="app-container">
    </div>
</div>

<script>
    localStorage.setItem('token', '<?=$this->context->getToken()?>')
</script>

<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <?php $this->registerJsFile('/js/main.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]); ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);

    $menuItems = [];

    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => 'Login', 'url' => ['/site/login']];
    } else {

        $projects = Yii::$app->user->getIdentity()->getProjects();

        $projectSelect = '<li style="padding: 15px;">'
            . Html::beginForm(['/project/select'], 'post')
            . '<select id="project-selector" style="width: 8vw;" value="'. Yii::$app->user->getIdentity()->getAttribute('active_project').'">';

        foreach ($projects as $project) {
            $selected = $project[0]->getAttribute('id') == Yii::$app->user->getIdentity()->getAttribute('active_project') ? 'selected' : '';
            $projectSelect .= "<option value=\"{$project[0]->getAttribute('id')}\" {$selected}>{$project[0]->getAttribute('name')}</option>";
        }

        $projectSelect .=
               '</select>'
            . Html::endForm()
            . '</li>';

        $menuItems[] = $projectSelect;
        $menuItems[] = ['label' => 'Log Data', 'url' => ['/log/index']];
        $menuItems[] = ['label' => 'Projects', 'url' => ['/project/index']];
        $menuItems[] = ['label' => 'Notification', 'url' => ['/notification/index']];
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';
    }

    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);

    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
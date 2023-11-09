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
    <script src="http://healthcheck.com/js/jsTrack.js"></script>
    <script>
        window.healthCheckTrackJs.install({
            key: '655ce4c0d8dcb161',
            log: false,
        })
    </script>
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

        $projectSelect = '<li style="padding: 8px;">'
            . Html::beginForm(['/project/select'], 'post')
            . '<select id="project-selector" class="form-control" style="width: 8vw;" value="'. Yii::$app->user->getIdentity()->getAttribute('active_project').'">';

        foreach ($projects as $project) {
            $selected = $project->getAttribute('id') == Yii::$app->user->getIdentity()->getAttribute('active_project') ? 'selected' : '';
            $projectSelect .= "<option value=\"{$project->getAttribute('id')}\" {$selected}>{$project->getAttribute('name')}</option>";
        }

        $projectSelect .=
               '</select>'
            . Html::endForm()
            . '</li>';

        $menuItems[] = ['label' => '<span class="glyphicon glyphicon-equalizer" aria-hidden="true"></span>Statistics',
            'url' => ['/stat/index'],'encode' => false];
        $menuItems[] = ['label' => '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>Log Data', 'url' => ['/log/index'],'encode' => false];
        $menuItems[] = ['label' => '<span class="glyphicon glyphicon-remove-sign" aria-hidden="true"></span>Js Errors', 'url' => ['/log/js'],'encode' => false];
        $menuItems[] = ['label' => '<span class="glyphicon glyphicon-cog" aria-hidden="true"></span>Settings', 'url' => ['/project/view'],'encode' => false];
        $menuItems[] = $projectSelect;
        $menuItems[] = ['label' => '<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>Add', 'url' => ['/project/create'],'encode' => false];
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '<span class="glyphicon glyphicon-off" aria-hidden="true"></span>Logout (' . Yii::$app->user->identity->username . ')',
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
        <div class="col-lg-10">
            <ul>
                <li>
                    <a href="/tools/list">Tools</a>
                </li>
<!--                <li>-->
<!--                    <a>Documentation</a>-->
<!--                </li>-->
            </ul>
        </div>
        <div class="col-lg-2">
            <p class="pull-left"><span class="glyphicon glyphicon-copyright-mark" aria-hidden="true"></span> Magenmagic Team <?= date('Y') ?></p>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>


</html>
<?php $this->endPage() ?>

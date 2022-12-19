<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $content string */
/* @var $message string */
/* @var $url string */

$this->title = 'Check .git/config';
$this->params['breadcrumbs'][] = ['label' => 'Tools list', 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="col-lg-12">
    <div class="col-lg-6">
        <form action="git" method="post" class="form-check">
            <div class="input-group" style="height: available">
                <input type="hidden" name="_csrf" value="<?=Yii::$app->request->getCsrfToken()?>" />
                <input type="text" class="form-control" placeholder="https://..." name="url">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-default" type="button">Check</button>
                </span>
            </div>
        </form>
    </div>
</div>
<div class="col-lg-12">
    <div class="col-lg-6">
        <?php if ($message) : ?>
            <?= $message ?>
        <?php endif;?>
    </div>
</div>



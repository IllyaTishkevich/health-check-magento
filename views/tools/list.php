<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'Tools list';
$this->params['breadcrumbs'][] = $this->title;
?>
<div style="width: fit-content">
    <div class="list-group">
        <a href="git" class="list-group-item">
            <h4 class="list-group-item-heading">Check .git/config</h4>
            <p class="list-group-item-text">
                Check if the site is available for vulnerability
                through the .git/config file.
            </p>
        </a>
    </div>

    <div class="list-group">
        <a href="#" class="list-group-item">
            <h4 class="list-group-item-heading">Magento version</h4>
            <p class="list-group-item-text">
                Check is a magento project.
            </p>
        </a>
    </div>
</div>
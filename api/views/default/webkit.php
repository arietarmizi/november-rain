<?php

/* @var $title
 * @var $date
 * @var $content
 */

$backgroundHeader = isset($image) ? $image : \yii\helpers\Url::to('@web/img/logo/ndc_red.png', true);
?>
<style>
    .site-wrapper {
        display: table;
        width: 100%;
        height: 256px;
        padding: 10px;
        color: #fff;
        overflow: hidden;
        background: url("<?=$backgroundHeader?>");
        background-repeat: no-repeat;
        background-size: 100% auto;
        background-position: center top;
        -webkit-box-shadow: inset 0 0 100px rgba(0, 0, 0, 0.7);
        box-shadow: inset 2000px 0 0 0 rgba(0, 0, 0, 0.7);
        border-color: rgba(0, 0, 0, 1);
    }
</style>
<div class="site-wrapper">
    <div class="site-wrapper-inner">
        <div class="cover-container">
            <div class="inner cover">
                <h4 class="text-center"><?= $title ?></h4>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 blog-main">
            <div class="blog-post">
                <p class="blog-post-meta">
                    <span class="badge badge-info"><?= $this->title ?></span>
                    <span class="pull-right"><span class="glyphicon glyphicon-calendar"></span> <?= \Yii::$app->formatter->asDate($date, 'php:d M Y, H:i'); ?></span>
                </p>
                <p><?= $content ?></p>
            </div>
        </div>
    </div>
</div>
<?php

use api\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;

Yii::setAlias('@web', Url::base(true));
Yii::$app->assetManager->baseUrl = Yii::getAlias('@web/assets');
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="content-type" content="text/html;charset=UTF-8">
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <?= $content ?>
    <footer class="blog-footer">
        <p>© Copyright <a href="https://pertaminalubricants/">Pertamina Lubricants</a> 2022.
        </p>
        <p>
            <a href="#">Back to top</a>
        </p>
    </footer>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
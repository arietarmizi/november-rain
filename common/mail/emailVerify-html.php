<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink = Yii::$app->frontendUrlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verificationToken]);
?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->name) ?>,</p>

    <p>Follow the link below to verify your email:</p>

    <p><?= Html::a(Html::encode($verifyLink), $verifyLink) ?></p>
</div>

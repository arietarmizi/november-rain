<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$verifyLink = Yii::$app->frontendUrlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->verificationToken]);
?>
Hello <?= $user->name ?>,

Follow the link below to verify your email:

<?= $verifyLink ?>

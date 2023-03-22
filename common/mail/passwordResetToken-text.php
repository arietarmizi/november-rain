<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->frontendUrlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->passwordResetToken]);
?>
Hello <?= $user->phoneNumber ?>,

Follow the link below to reset your password:

<?= $resetLink ?>

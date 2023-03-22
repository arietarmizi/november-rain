<?php

use Carbon\Carbon;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $template array */
?>
<p>
    <?= Yii::t('app', 'Hello') ?>
    <b><?= Html::encode($template['userName']) ?></b>,
    <?= Yii::t('app', 'Please use this code to reset the password:') ?>
</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
    <tbody>
    <tr>
        <td align="middle">
            <span style="color:#ec1c24;font-size: 50px"><?= Html::encode($template['code']) ?></span>
        </td>
    </tr>
    </tbody>
</table>
<p>Expired until <b><?= Carbon::parse(Html::encode($template['expiredTime']))->format('d F Y H:i:s') ?></b></p>
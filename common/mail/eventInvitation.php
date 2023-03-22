<?php

use yii\helpers\ArrayHelper;

$apiBaseUrl = ArrayHelper::getValue(\Yii::$app->params, 'api.baseUrl', \Yii::$app->urlManager->baseUrl);
?>
<p>Halo,<?= $participant->name; ?> anda diundang untuk menghadiri event <b><?= $event->name ?></b>
</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0"
       class="btn btn-primary">
    <tbody>
    <tr>
        <th align="left">
            Nama Event
        </th>
        <td align="left"><?= $event->name ?></td>
    </tr>
    <tr>
        <th align="left">
            Desckripsi
        </th>
        <td align="left"><?= $event->description ?></td>
    </tr>
    <tr>
        <th align="left">
            Tanggal Mulai
        </th>
        <td align="left"><?= Yii::$app->formatter->asDate($event->start) ?></td>
    </tr>
    <tr>
        <td align="left">
            <table role="presentation" border="0" cellpadding="0"
                   cellspacing="0">
                <tbody>
                <tr>
                    <td><a href="<?= $apiBaseUrl . '/' . $event->id ?>" target="_blank">Login</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<p>Klik tombol diatas melihat detail event, Terima kasih.</p>
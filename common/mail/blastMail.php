<p>Halo,<b><?= $participant->user->name; ?></b> berikut data anda untuk login:</p>
<table role="presentation" border="0" cellpadding="0" cellspacing="0"
       class="btn btn-primary">
    <tbody>
    <tr>
        <th align="left">
            Username
        </th>
        <td align="left"><?= $participant->user->phoneNumber ?></td>
    </tr>
    <tr>
        <th align="left">
            Password
        </th>
        <td align="left"><?= $participant->user->phoneNumber ?></td>
    </tr>
    <tr>
        <td align="left">
            <table role="presentation" border="0" cellpadding="0"
                   cellspacing="0">
                <tbody>
                <tr>
                    <td><a href="<?= \Yii::$app->frontendUrlManager->createUrl(['login']) ?>" target="_blank">Klik Untuk
                            Login</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<p>Terima kasih.</p>
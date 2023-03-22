<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%device}}`.
 */
class m230307_100543_create_device_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Device}}';

        $this->createTable($tableName, [
            'Id'            => $this->string(),
            'UserId'        => $this->string(36)->notNull(),
            'AccessToken'   => $this->string()->notNull(),
            'FirebaseToken' => $this->string(),
            'Identifier'    => $this->string(),
            'OsType'        => $this->string(),
            'OsVersion'     => $this->string(),
            'PlayerId'      => $this->string(),
            'Model'         => $this->string(),
            'AppVersion'    => $this->string(),
            'Latitude'      => $this->string(),
            'Longitude'     => $this->string(),
            'LastIp'        => $this->string(),
            'Language'      => $this->string()->defaultValue('id'),
            'Timezone'      => $this->string()->defaultValue('Asia/Jakarta'),
            'Status'        => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'UserId', '{{%User}}', 'Id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Device}}');
    }
}

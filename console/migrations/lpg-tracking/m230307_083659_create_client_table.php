<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%client}}`.
 */
class m230307_083659_create_client_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Client}}';

        $this->createTable($tableName, [
            'Id'       => $this->string(),
            'Name'     => $this->string()->notNull(),
            'Key'      => $this->string(50)->notNull(),
            'Secret'   => $this->string()->notNull(),
            'Grant'    => $this->string(),
            'Type'     => $this->string(50)->comment('[Device, Service]'),
            'IPs'      => $this->string(),
            'ExpiryOn' => $this->dateTime(),
            'Status'   => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->addLogColumns($tableName);
        $this->createStatusIndex($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Client}}');
    }
}

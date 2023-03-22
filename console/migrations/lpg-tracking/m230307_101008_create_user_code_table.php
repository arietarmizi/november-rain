<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%user_code}}`.
 */
class m230307_101008_create_user_code_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%UserCode}}';

        $this->createTable($tableName, [
            'Id'        => $this->string(),
            'UserId'    => $this->string(36)->notNull(),
            'Key'       => $this->string(100)->notNull(),
            'Value'     => $this->string(50)->notNull(),
            'RefId'     => $this->string(36),
            'UsedOn'    => $this->dateTime(),
            'ExpiredAt' => $this->dateTime(),
            'Status'    => $this->string(50)->defaultValue('Active'),
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
        $this->dropTable('{{%UserCode}}');
    }
}

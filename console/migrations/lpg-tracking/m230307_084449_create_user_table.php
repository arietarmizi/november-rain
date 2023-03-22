<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%user}}`.
 */
class m230307_084449_create_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%User}}';

        $this->createTable($tableName, [
            'Id'           => $this->string(),
            'Name'         => $this->string()->notNull(),
            'Identity'     => $this->string()->notNull(),
            'PasswordHash' => $this->string(),
            'PhoneNumber'  => $this->string(20),
            'Address'      => $this->text(),
            'VerifiedOn'   => $this->dateTime()->null(),
            'Status'       => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%User}}');
    }
}

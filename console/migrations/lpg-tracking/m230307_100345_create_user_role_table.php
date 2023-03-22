<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%user_role}}`.
 */
class m230307_100345_create_user_role_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%UserRole}}';

        $this->createTable($tableName, [
            'Id'            => $this->string(),
            'UserId'        => $this->string(36)->notNull(),
            'Role'          => $this->string(50)->notNull(),
            'AgentId'       => $this->string(36)->null(),
            'TransporterId' => $this->string(36)->null(),
            'OutletId'      => $this->string(36)->null(),
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
        $this->dropTable('{{%UserRole}}');
    }
}

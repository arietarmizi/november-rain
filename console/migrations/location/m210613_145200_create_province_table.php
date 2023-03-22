<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%province}}`.
 */
class m210613_145200_create_province_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Province}}';

        $this->createTable($tableName, [
            'Id'     => $this->string(),
            'Name'   => $this->string(255)->unique()->notNull(),
            'Status' => $this->string(50)->defaultValue('Active'),
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
        $this->dropTable('{{%Province}}');
    }
}

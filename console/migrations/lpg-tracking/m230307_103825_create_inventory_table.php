<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%inventory}}`.
 */
class m230307_103825_create_inventory_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Inventory}}';

        $this->createTable($tableName, [
            'Id'         => $this->string(),
            'AgentId'    => $this->string(36)->notNull(),
            'MaterialId' => $this->string(36)->notNull(),
            'Stock'      => $this->double(),
            'Reserved'   => $this->double(),
            'Out'        => $this->double(),
            'Status'     => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'AgentId', '{{%Agent}}', 'Id');
        $this->setForeignKey($tableName, 'MaterialId', '{{%Material}}', 'Id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Inventory}}');
    }
}

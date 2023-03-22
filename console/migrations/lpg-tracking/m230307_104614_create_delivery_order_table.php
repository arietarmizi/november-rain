<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%deliver_order}}`.
 */
class m230307_104614_create_delivery_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%DeliveryOrder}}';

        $this->createTable($tableName, [
            'Id'            => $this->string(),
            'AgentId'       => $this->string(36)->notNull(),
            'OutletId'      => $this->string(36)->notNull(),
            'Code'          => $this->string(36)->notNull(),
            'PaymentMethod' => $this->string(100)->defaultValue('Cash'),
            'TotalReserved' => $this->double(),
            'TotalReceived' => $this->double(),
            'Status'        => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'AgentId', '{{%Agent}}', 'Id');
        $this->setForeignKey($tableName, 'OutletId', '{{%Outlet}}', 'Id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%DeliveryOrder}}');
    }
}

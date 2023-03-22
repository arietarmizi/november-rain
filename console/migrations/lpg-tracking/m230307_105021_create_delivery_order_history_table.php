<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%deliver_order_history}}`.
 */
class m230307_105021_create_delivery_order_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%DeliveryOrderHistory}}';

        $this->createTable($tableName, [
            'Id'                      => $this->string(),
            'DeliveryOrderId'         => $this->string(36)->notNull(),
            'DeliveryOrderScheduleId' => $this->string(36),
            'Procedure'               => $this->string(50)->notNull()->defaultValue('Draft'),
            'Log'                     => $this->string(),
            'Status'                  => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'DeliveryOrderId', '{{%DeliveryOrder}}', 'Id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%DeliveryOrderHistory}}');
    }
}

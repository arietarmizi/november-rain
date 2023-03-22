<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%deliver_order_schedule}}`.
 */
class m230307_105002_create_delivery_order_schedule_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%DeliveryOrderSchedule}}';

        $this->createTable($tableName, [
            'Id'                   => $this->string(),
            'DeliveryOrderId'      => $this->string(36)->notNull(),
            'TransporterId'        => $this->string(36)->notNull(),
            'VehicleId'            => $this->string(36)->notNull(),
            'ScheduledAt'          => $this->dateTime(),
            'EstimatedTimeArrival' => $this->dateTime(),
            'DeliveryOn'           => $this->dateTime(),
            'ReceivingOn'          => $this->dateTime(),
            'IsRejected'           => $this->boolean()->null(),
            'RejectReason'         => $this->string(),
            'AllowReschedule'      => $this->boolean()->defaultValue(0),
            'RescheduleAfter'      => $this->dateTime(),
            'Action'               => $this->string(),
            'Status'               => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'DeliveryOrderId', '{{%DeliveryOrder}}', 'Id');
        $this->setForeignKey($tableName, 'TransporterId', '{{%User}}', 'Id');
        $this->setForeignKey($tableName, 'VehicleId', '{{%Vehicle}}', 'Id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%DeliveryOrderSchedule}}');
    }
}

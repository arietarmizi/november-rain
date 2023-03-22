<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%deliver_order_item}}`.
 */
class m230307_104939_create_delivery_order_material_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%DeliveryOrderMaterial}}';

        $this->createTable($tableName, [
            'Id'              => $this->string(),
            'DeliveryOrderId' => $this->string(36)->notNull(),
            'LoadingOrderId'  => $this->string(36)->notNull(),
            'MaterialId'      => $this->string(36)->notNull(),
            'Reserved'        => $this->double()->notNull(),
            'Received'        => $this->double(),
            'NoteReceipt'     => $this->string(),
            'Status'          => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'DeliveryOrderId', '{{%DeliveryOrder}}', 'Id');
        $this->setForeignKey($tableName, 'LoadingOrderId', '{{%LoadingOrder}}', 'Id');
        $this->setForeignKey($tableName, 'MaterialId', '{{%Material}}', 'Id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%DeliveryOrderMaterial}}');
    }
}

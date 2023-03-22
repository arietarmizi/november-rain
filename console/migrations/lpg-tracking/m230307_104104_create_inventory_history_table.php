<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%inventory_history}}`.
 */
class m230307_104104_create_inventory_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%InventoryHistory}}';

        $this->createTable($tableName, [
            'Id'           => $this->string(),
            'InventoryId'  => $this->string(36)->notNull(),
            'RefSource'    => $this->string(50)->notNull()->comment('[LoadingOrder, DeliveryOrder, Conversion, Returned, Damaged, Other]'),
            'RecordedTime' => $this->dateTime(),
            'RefCode'      => $this->string(),
            'Quantity'     => $this->double(),
            'Log'          => $this->string(),
            'Status'       => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'InventoryId', '{{%Inventory}}', 'Id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%InventoryHistory}}');
    }
}

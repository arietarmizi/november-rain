<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%loading_order_material}}`.
 */
class m230307_103624_create_loading_order_material_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%LoadingOrderMaterial}}';

        $this->createTable($tableName, [
            'Id'             => $this->string(),
            'LoadingOrderId' => $this->string(36)->notNull(),
            'MaterialId'     => $this->string(36)->notNull(),
            'ItemCode'       => $this->string(),
            'Quantity'       => $this->double()->notNull(),
            'Status'         => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'LoadingOrderId', '{{%LoadingOrder}}', 'Id');
        $this->setForeignKey($tableName, 'MaterialId', '{{%Material}}', 'Id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%LoadingOrderMaterial}}');
    }
}

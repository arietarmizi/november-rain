<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%deliver_order_evidence}}`.
 */
class m230307_105014_create_delivery_order_evidence_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%DeliveryOrderEvidence}}';

        $this->createTable($tableName, [
            'Id'              => $this->string(),
            'DeliveryOrderId' => $this->string(36)->notNull(),
            'MediaId'         => $this->string(36)->notNull(),
            'Url'             => $this->string()->notNull(),
            'UrlThumbnail'    => $this->string(),
            'Status'          => $this->string(50)->defaultValue('Active'),
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
        $this->dropTable('{{%DeliveryOrderEvidence}}');
    }
}

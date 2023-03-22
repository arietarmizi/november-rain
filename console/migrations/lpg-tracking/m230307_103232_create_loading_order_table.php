<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%loading_order}}`.
 */
class m230307_103232_create_loading_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%LoadingOrder}}';

        $this->createTable($tableName, [
            'Id'                => $this->string(),
            'AgentId'           => $this->string(36)->notNull(),
            'Code'              => $this->string()->notNull()->unique(),
            'Date'              => $this->date()->notNull(),
            'DeliveryCode'      => $this->string(50),
            'DeliveryDate'      => $this->date(),
            'DeliveryMethod'    => $this->string(50),
            'ShipmentCondition' => $this->string(100),
            'ShipmentOrigin'    => $this->string(),
            'VehicleNumber'     => $this->string(),
            'DriverNumber'      => $this->string(),
            'FillingPoint'      => $this->string(),
            'SealNumber'        => $this->string(),
            'Status'            => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'AgentId', '{{%Agent}}', 'Id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%LoadingOrder}}');
    }
}

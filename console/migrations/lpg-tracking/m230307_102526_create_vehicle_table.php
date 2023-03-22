<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%vehicle}}`.
 */
class m230307_102526_create_vehicle_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Vehicle}}';

        $this->createTable($tableName, [
            'Id'                 => $this->string(),
            'AgentId'            => $this->string(36)->notNull(),
            'Code'               => $this->string(50)->notNull(),
            'Type'               => $this->string()->notNull(),
            'Name'               => $this->string()->notNull(),
            'Capacity'           => $this->double(),
            'Description'        => $this->text(),
            'LicensePlateNumber' => $this->string(20)->notNull(),
            'Status'             => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'AgentId', '{{%Agent}}', 'Id');
        $this->createIndex('UniqueVehicle', $tableName, ['AgentId', 'Code'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Vehicle}}');
    }
}

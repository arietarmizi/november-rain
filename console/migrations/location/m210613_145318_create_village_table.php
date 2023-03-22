<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%village}}`.
 */
class m210613_145318_create_village_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Village}}';

        $this->createTable($tableName, [
            'Id'         => $this->string(),
            'DistrictId' => $this->string(36)->notNull(),
            'Name'       => $this->string(255)->notNull(),
            'PostalCode' => $this->string(36)->notNull(),
            'Status'     => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->addLogColumns($tableName);

        $this->createIndex('UniqueVillageName', $tableName, ['districtId', 'Name', 'PostalCode'], true);
        $this->setForeignKey($tableName, 'DistrictId', '{{%District}}', 'Id');
        $this->createStatusIndex($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Village}}');
    }
}

<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%district}}`.
 */
class m210613_145312_create_district_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%District}}';

        $this->createTable($tableName, [
            'Id'        => $this->string(),
            'RegencyId' => $this->string(36)->notNull(),
            'Name'      => $this->string(255)->notNull(),
            'Status'    => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->addLogColumns($tableName);

        $this->createIndex('UniqueDistrictName', $tableName, ['RegencyId', 'Name'], true);
        $this->setForeignKey($tableName, 'RegencyId', '{{%Regency}}', 'Id');
        $this->createStatusIndex($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%District}}');
    }
}

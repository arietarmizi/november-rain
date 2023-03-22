<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%region_province}}`.
 */
class m230307_101418_create_region_province_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%RegionProvince}}';

        $this->createTable($tableName, [
            'Id'         => $this->string(),
            'RegionId'   => $this->string(36)->notNull(),
            'ProvinceId' => $this->string(36)->notNull(),
            'Status'     => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'RegionId', '{{%Region}}', 'Id');
        $this->createIndex('UniqueRegionProvince', $tableName, ['RegionId', 'ProvinceId'], true);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%RegionProvince}}');
    }
}

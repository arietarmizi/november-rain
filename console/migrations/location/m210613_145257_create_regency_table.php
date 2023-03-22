<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%regency}}`.
 */
class m210613_145257_create_regency_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{Regency}}';

        $this->createTable($tableName, [
            'Id'         => $this->string(),
            'ProvinceId' => $this->string(36)->notNull(),
            'Code'       => $this->string(50)->notNull(),
            'Name'       => $this->string(255)->notNull(),
            'Status'     => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->addLogColumns($tableName);

        $this->createIndex('UniqueRegencyName', $tableName, ['ProvinceId', 'Name'], true);
        $this->setForeignKey($tableName, 'ProvinceId', '{{%Province}}', 'Id');
        $this->createStatusIndex($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Regency}}');
    }
}

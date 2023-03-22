<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%region}}`.
 */
class m230307_101314_create_region_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Region}}';

        $this->createTable($tableName, [
            'Id'          => $this->string(),
            'Code'        => $this->string(50)->notNull(),
            'Name'        => $this->string(100)->notNull(),
            'Description' => $this->text(),
            'Status'      => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Region}}');
    }
}

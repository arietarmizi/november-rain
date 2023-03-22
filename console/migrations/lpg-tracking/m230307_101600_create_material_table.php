<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%material}}`.
 */
class m230307_101600_create_material_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Material}}';

        $this->createTable($tableName, [
            'Id'          => $this->string(),
            'Code'        => $this->string(50)->notNull(),
            'Name'        => $this->string()->notNull(),
            'Description' => $this->text(),
            'Unit'        => $this->string(50),
            'Dimension'   => $this->double(),
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
        $this->dropTable('{{%Material}}');
    }
}

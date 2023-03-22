<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%material_conversion}}`.
 */
class m230307_101848_create_material_conversion_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%MaterialConversion}}';

        $this->createTable($tableName, [
            'Id'          => $this->string(),
            'MaterialId'  => $this->string(36)->notNull(),
            'Name'        => $this->string()->notNull(),
            'Description' => $this->text(),
            'IsDefault'   => $this->boolean()->defaultValue(1),
            'Status'      => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'MaterialId', '{{%Material}}', 'Id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%MaterialConversion}}');
    }
}

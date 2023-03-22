<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%material_conversion_formula}}`.
 */
class m230307_101910_create_material_conversion_formula_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%MaterialConversionFormula}}';

        $this->createTable($tableName, [
            'Id'                   => $this->string(),
            'MaterialConversionId' => $this->string(36)->notNull(),
            'MaterialId'           => $this->string(36)->notNull(),
            'Quantity'             => $this->double(),
            'Status'               => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'MaterialConversionId', '{{%MaterialConversion}}', 'Id');
        $this->setForeignKey($tableName, 'MaterialId', '{{%Material}}', 'Id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%MaterialConversionFormula}}');
    }
}

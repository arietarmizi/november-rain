<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%agent}}`.
 */
class m230307_102340_create_agent_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Agent}}';

        $this->createTable($tableName, [
            'Id'           => $this->string(),
            'ProvinceId'   => $this->string(36)->notNull(),
            'RegionId'     => $this->string(36)->notNull(),
            'Code'         => $this->string(50)->notNull()->unique(),
            'Type'         => $this->string()->notNull(),
            'Name'         => $this->string()->notNull(),
            'PhoneNumber'  => $this->string(20),
            'Address'      => $this->text(),
            'Neighborhood' => $this->string(5),
            'Hamlet'       => $this->string(5),
            'Status'       => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'RegionId', '{{%Region}}', 'Id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Agent}}');
    }
}

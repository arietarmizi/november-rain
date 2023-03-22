<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%outlet}}`.
 */
class m230307_102817_create_outlet_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%Outlet}}';

        $this->createTable($tableName, [
            'Id'           => $this->string(),
            'AgentId'      => $this->string(36),
            'VillageId'    => $this->string(36),
            'Code'         => $this->string(50)->notNull()->unique(),
            'Name'         => $this->string()->notNull(),
            'PhoneNumber'  => $this->string(20),
            'Address'      => $this->text(),
            'Neighborhood' => $this->string(5),
            'Hamlet'       => $this->string(5),
            'Wide'         => $this->double(),
            'Latitude'     => $this->double(),
            'Longitude'    => $this->double(),
            'Status'       => $this->string(50)->defaultValue('Active'),
        ]);

        $this->setPrimaryUUID($tableName, 'Id');
        $this->createStatusIndex($tableName);
        $this->addLogColumns($tableName);

        $this->setForeignKey($tableName, 'AgentId', '{{%Agent}}', 'Id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%Outlet}}');
    }
}

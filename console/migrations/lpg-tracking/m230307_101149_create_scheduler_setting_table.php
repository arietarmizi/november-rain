<?php

use console\components\Migration;

/**
 * Handles the creation of table `{{%scheduler_setting}}`.
 */
class m230307_101149_create_scheduler_setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = '{{%SchedulerSetting}}';

        $this->createTable($tableName, [
            'Id'          => $this->string(),
            'Key'         => $this->string(100)->notNull(),
            'Description' => $this->text(),
            'Frequency'   => $this->double()->notNull(),
            'Period'      => $this->string(20)->notNull(),
            'IsRunning'   => $this->boolean()->defaultValue(0),
            'SyncOn'      => $this->dateTime(),
            'LastResult'  => $this->text(),
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
        $this->dropTable('{{%SchedulerSetting}}');
    }
}

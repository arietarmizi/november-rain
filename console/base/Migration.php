<?php

namespace console\base;

class Migration extends \yii\db\Migration
{
    /**
     * @var string
     */
    protected $tableOptions;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();


        echo 'bupind ='. $this->getDb()->driverName;
        // switch based on driver name
        switch ($this->getDb()->driverName) {

            case 'mysql':
                $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
                break;
            default:
                $this->tableOptions = null;
        }
    }

    public function formatForeignKeyName($tableNameForeign, $tableNamePrimary)
    {
        
        $tableNameForeign = explode('.', $tableNameForeign);
        $tableNameForeign = $tableNameForeign[count($tableNameForeign)-1];
        $tableNameForeign = trim($tableNameForeign, '{}');
        $tableNameForeign = str_replace('%', '', $tableNameForeign);
        
     $tableNamePrimary = explode('.', $tableNamePrimary);
        $tableNamePrimary = $tableNamePrimary[count($tableNamePrimary)-1];
        $tableNamePrimary = trim($tableNamePrimary, '{}');
        $tableNamePrimary = str_replace('%', '', $tableNamePrimary);

        return 'fk_' . $tableNameForeign . '_' . $tableNamePrimary;
    }

    public function setForeignKey($table, $columns, $refTable, $refColumns, $delete = 'CASCADE', $update = 'CASCADE')
    {
        $name = $this->formatForeignKeyName($table, $refTable);
        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }
}

<?php

namespace console\components;

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

        // switch based on driver name
        switch ($this->getDb()->driverName) {
            case 'mysql':
                $this->tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
                break;
            default:
                $this->tableOptions = null;
        }
    }

    public function setForeignKey(
        $table,
        $columns,
        $refTable,
        $refColumns,
        $delete = 'NO ACTION',
        $update = 'NO ACTION'
    ) {
        $name = $this->formatForeignKeyName($table, $refTable);
        parent::addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete, $update);
    }

    public function formatForeignKeyName($tableNameForeign, $tableNamePrimary)
    {
        $tableNameForeign = explode('.', $tableNameForeign);
        $tableNameForeign = $tableNameForeign[count($tableNameForeign) - 1];
        $tableNameForeign = trim($tableNameForeign, '{}');
        $tableNameForeign = str_replace('%', '', $tableNameForeign);
        $tableNameForeign = str_replace(' ', '', ucwords(str_replace('_', ' ', $tableNameForeign)));

        $tableNamePrimary = explode('.', $tableNamePrimary);
        $tableNamePrimary = $tableNamePrimary[count($tableNamePrimary) - 1];
        $tableNamePrimary = trim($tableNamePrimary, '{}');
        $tableNamePrimary = str_replace('%', '', $tableNamePrimary);
        $tableNamePrimary = str_replace(' ', '', ucwords(str_replace('_', ' ', $tableNamePrimary)));

        return 'FK' . $tableNameForeign . $tableNamePrimary;
    }

    public function setPrimaryUUID($table, $column)
    {
        $originalTableName = explode('.', $table);
        $originalTableName = $originalTableName[count($originalTableName) - 1];
        $originalTableName = trim($originalTableName, '{}');
        $originalTableName = str_replace('%', '', $originalTableName);
        $originalTableName = str_replace(' ', '', ucwords(str_replace('_', ' ', $originalTableName)));

        $this->alterColumn($table, $column, $this->string(36)->notNull());
        $this->addPrimaryKey('PK' . $originalTableName . ucwords($column), $table, $column);
    }

    public function addLogColumns($table)
    {
        $this->addColumn($table, 'CreatedAt', $this->dateTime());
        $this->addColumn($table, 'CreatedBy', $this->string());
        $this->addColumn($table, 'UpdatedAt', $this->dateTime());
        $this->addColumn($table, 'UpdatedBy', $this->string());

        $originalTableName = explode('.', $table);
        $originalTableName = $originalTableName[count($originalTableName) - 1];
        $originalTableName = trim($originalTableName, '{}');
        $originalTableName = str_replace('%', '', $originalTableName);
        $originalTableName = str_replace(' ', '', ucwords(str_replace('_', ' ', $originalTableName)));

        $this->createIndex("Index" . $originalTableName . 'CreatedAt', $table, "CreatedAt");
        $this->createIndex("Index" . $originalTableName . 'UpdatedAt', $table, "UpdatedAt");
    }

    public function createTable($table, $columns, $options = null)
    {
        parent::createTable($table, $columns, $this->tableOptions);
    }

    public function createStatusIndex($table, $column = 'Status')
    {
        $originalTableName = explode('.', $table);
        $originalTableName = $originalTableName[count($originalTableName) - 1];
        $originalTableName = trim($originalTableName, '{}');
        $originalTableName = str_replace('%', '', $originalTableName);
        $originalTableName = str_replace(' ', '', ucwords(str_replace('_', ' ', $originalTableName)));

        $this->createIndex('Index' . $originalTableName . ucwords($column), $table, $column);
    }
}

<?php

use app\migrations\RcMigration;

/**
 * Class m210311_141713_create_table_materials
 */
class m210311_141713_create_table_materials extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        if (!$this->tableExists($this::TABLE_MATERIALS)) {
            $this->createTable($this::TABLE_MATERIALS, [
                'id' => $this->primaryKey()->comment('ИД'),
                'ref' => $this->bigInteger(20)->notNull()->unique()->comment('Номер САП'),
                'name' => $this->string(128)->notNull()->comment('Наименование'),
                'qty' => $this->decimal(7, 3)->defaultValue(0)->comment('Текущее кол-во'),
                'min_qty' => $this->decimal(7, 3)->defaultValue(0)->comment('Крит. низкое кол-во'),
                'max_qty' => $this->decimal(7, 3)->defaultValue(1)->comment('Макс. кол-во'),
                'unit' => $this->tinyInteger(2)->notNull()->defaultValue(1)->comment('Ед. измерения'),
                'type' => $this->string(16)->defaultValue(null)->comment('Тип'),
                'group' => $this->string(16)->defaultValue(null)->comment('Группа'),
                'created_at' => $this->dateTime()->comment('Дата добавления'),
                'updated_at' => $this->dateTime()->comment('Дата изменения'),
                'created_by' => $this->integer()->comment('Кто добавил'),
                'updated_by' => $this->integer()->comment('Кто отредактировал'),
            ], $tableOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->tableExists($this::TABLE_MATERIALS)) {
            $this->dropTable($this::TABLE_MATERIALS);
        }
    }
}

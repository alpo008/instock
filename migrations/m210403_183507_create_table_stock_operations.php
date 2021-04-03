<?php

use app\migrations\RcMigration;

/**
 * Class m210403_183507_create_table_stock_operations
 */
class m210403_183507_create_table_stock_operations extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$this->tableExists(self::TABLE_STOCK_OPERATIONS)) {
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            } else {
                $tableOptions = '';
            }

            $this->createTable(self::TABLE_STOCK_OPERATIONS, [
                'id' => $this->primaryKey()->comment('ИД'),
                'material_id' => $this->integer(11)->notNull()->comment('ИД материала'),
                'stock_id' => $this->integer(11)->notNull()->comment('ИД складского места'),
                'operation_type' => $this->tinyInteger(1)->notNull()->comment('Тип операции'),
                'qty' => $this->decimal(7, 3)->comment('Количество материала'),
                'from_to' => $this->string(64)->notNull()->comment('Источник или получатель'),
                'comments' => $this->text()->comment('Комментарий'),
                'created_at' => $this->dateTime()->comment('Дата добавления'),
                'created_by' => $this->integer()->comment('Кто создал')
            ], $tableOptions);

            $this->createIndex('uk-s_o_material_id_stock_id', self::TABLE_STOCK_OPERATIONS,
                ['material_id', 'stock_id'], false
            );
            $this->createIndex('uk-s_o_operation_type', self::TABLE_STOCK_OPERATIONS,
                ['operation_type'], false
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->tableExists($this::TABLE_STOCK_OPERATIONS)) {
            $this->dropIndex('uk-s_o_material_id_stock_id', self::TABLE_STOCK_OPERATIONS);
            $this->dropIndex('uk-s_o_operation_type', self::TABLE_STOCK_OPERATIONS);
            $this->dropTable($this::TABLE_STOCK_OPERATIONS);
        }
    }
}

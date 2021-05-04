<?php

use app\migrations\RcMigration;

/**
 * Class m210503_094834_update_table_materials_2
 */
class m210503_094834_update_table_materials_2 extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->columnExists(self::TABLE_MATERIALS, 'group')) {
            $this->alterColumn(self::TABLE_MATERIALS, 'group',
                $this->string(32)->defaultValue(null)->comment('Группа')
            );
        }
        if ($this->columnExists(self::TABLE_MATERIALS, 'type')) {
            $this->alterColumn(self::TABLE_MATERIALS, 'type',
                $this->string(32)->defaultValue(null)->comment('Тип')
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->columnExists(self::TABLE_MATERIALS, 'group')) {
            $this->alterColumn(self::TABLE_MATERIALS, 'group',
                $this->string(16)->defaultValue(null)->comment('Группа')
            );
        }
        if ($this->columnExists(self::TABLE_MATERIALS, 'type')) {
            $this->alterColumn(self::TABLE_MATERIALS, 'type',
                $this->string(16)->defaultValue(null)->comment('Тип')
            );
        }
    }
}

<?php

use app\migrations\RcMigration;

/**
 * Class m210401_124457_update_table_materials_1
 */
class m210401_124457_update_table_materials_1 extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->columnExists(self::TABLE_MATERIALS, 'qty')) {
            $this->dropColumn(self::TABLE_MATERIALS, 'qty');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (!$this->columnExists(self::TABLE_MATERIALS, 'qty')) {
            $this->addColumn(self::TABLE_MATERIALS, 'qty',
                $this->decimal(7, 3)->defaultValue(0)->comment('Текущее кол-во')
            );
        }
    }
}

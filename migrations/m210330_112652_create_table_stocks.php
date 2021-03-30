<?php

use app\migrations\RcMigration;

/**
 * Class m210330_112652_create_table_stocks
 */
class m210330_112652_create_table_stocks extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$this->tableExists(self::TABLE_STOCKS)) {
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            } else {
                $tableOptions = '';
            }

            $this->createTable(self::TABLE_STOCKS, [
                'id' => $this->primaryKey()->comment('ИД'),
                'alias' => $this->string(32)->notNull()->unique()->comment('Обозначение'),
                'description' => $this->text()->comment('Описание')
            ], $tableOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->tableExists(self::TABLE_STOCKS)) {
            $this->dropTable(self::TABLE_STOCKS);
        }
    }
}

<?php

use app\migrations\RcMigration;

/**
 * Handles the creation of table `{{%materials_stocks}}`.
 */
class m210330_141459_create_table_materials_stocks extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!$this->tableExists(self::TABLE_MATERIALS_STOCKS)) {
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            } else {
                $tableOptions = '';
            }

            $this->createTable(self::TABLE_MATERIALS_STOCKS, [
                'id' => $this->primaryKey()->comment('ИД'),
                'material_id' => $this->integer(11)->notNull()->comment('ИД материала'),
                'stock_id' => $this->integer(11)->notNull()->comment('ИД складского места'),
                'qty' => $this->decimal(7, 3)->comment('Количество')
            ], $tableOptions);

            $this->createIndex('uk-m_s_material_id_stock_id', self::TABLE_MATERIALS_STOCKS,
                ['material_id', 'stock_id'], true
            );

            $this->addForeignKey('fk-m_s_material_id-m_id', self::TABLE_MATERIALS_STOCKS,
                'material_id', self::TABLE_MATERIALS, 'id', 'RESTRICT', 'CASCADE'
            );
            $this->addForeignKey('fk-m_s_stock_id-s_id', self::TABLE_MATERIALS_STOCKS,
                'stock_id', self::TABLE_STOCKS, 'id', 'RESTRICT', 'CASCADE'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->tableExists(self::TABLE_MATERIALS_STOCKS)) {
            $this->dropForeignKey('fk-m_s_material_id-m_id', self::TABLE_MATERIALS_STOCKS);
            $this->dropForeignKey('fk-m_s_stock_id-s_id', self::TABLE_MATERIALS_STOCKS);
            $this->dropIndex('uk-m_s_material_id_stock_id', self::TABLE_MATERIALS_STOCKS);
            $this->dropTable(self::TABLE_MATERIALS_STOCKS);
        }
    }
}

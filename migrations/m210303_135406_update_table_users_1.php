<?php

use app\migrations\RcMigration;

/**
 * Class m210303_135406_update_table_users_1
 */
class m210303_135406_update_table_users_1 extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->tableExists($this::TABLE_USERS) && !$this->columnExists($this::TABLE_USERS, 'position')) {
            $this->addColumn($this::TABLE_USERS, 'position',
                $this->string(128)
                    ->notNull()
                    ->comment('Должность')
                    ->after('surname')
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->tableExists($this::TABLE_USERS) && !$this->columnExists($this::TABLE_USERS, 'position')) {
            $this->dropColumn($this::TABLE_USERS, 'position');
        }
    }
}

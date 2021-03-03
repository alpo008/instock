<?php

use app\migrations\RcMigration;

/**
 * Class m210303_091305_create_table_users
 */
class m210303_091305_create_table_users extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        if (!$this->tableExists($this::TABLE_USERS)) {
            $this->createTable($this::TABLE_USERS, [
                'id' => $this->primaryKey(),
                'username' => $this->string()->notNull()->unique()->comment('Логин'),
                'password_hash' => $this->string()->notNull()->comment('Пароль'),
                'auth_key' => $this->string(32)->notNull(),
                'email' => $this->string()->notNull()->unique()->comment('E-mail'),
                'role' => "ENUM('USER','ADMIN') NOT NULL  DEFAULT 'USER' COMMENT 'Роль'",
                'status' => $this->smallInteger()->notNull()->defaultValue(1)->comment('Статус'),
                'created_at' => $this->dateTime()->notNull()->comment('Дата добавления'),
                'updated_at' => $this->dateTime()->notNull()->comment('Дата изменения'),
            ], $tableOptions);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->tableExists($this::TABLE_USERS)) {
            $this->dropTable($this::TABLE_USERS);
        }
    }
}

<?php

use app\migrations\RcMigration;
use app\models\User;

/**
 * Class m210303_103432_seed_table_users
 */
class m210303_103432_seed_table_users extends RcMigration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->tableExists($this::TABLE_USERS) && !$this->columnExists($this::TABLE_USERS, 'name')) {
            $this->addColumn($this::TABLE_USERS, 'name',
                $this->string(64)
                ->notNull()
                    ->comment('Имя')
                    ->after('username')
            );
        }
        if ($this->tableExists($this::TABLE_USERS) && !$this->columnExists($this::TABLE_USERS, 'surname')) {
            $this->addColumn($this::TABLE_USERS, 'surname',
                $this->string(64)
                ->notNull()
                    ->comment('Фамилия')
                    ->after('name')
            );
        }
        $user = new User();
        $user->username = 'alexey';
        $user->name = Yii::$app->params['adminName'];;
        $user->surname = Yii::$app->params['adminSurname'];;
        $user->email = Yii::$app->params['adminEmail'];
        $user->role = User::ROLE_ADMIN;
        $user->setPassword(Yii::$app->params['adminPassword']);
        $user->generateAuthKey();
        $user->save();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if ($this->tableExists($this::TABLE_USERS) && $this->columnExists($this::TABLE_USERS, 'name')) {
            $this->dropColumn($this::TABLE_USERS, 'name');
        }
        if ($this->tableExists($this::TABLE_USERS) && $this->columnExists($this::TABLE_USERS, 'surname')) {
            $this->dropColumn($this::TABLE_USERS, 'surname');
        }
        if ($user = User::findOne(['username' => 'alexey'])) {
            $user->delete();
        }
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210303_103432_seed_table_users cannot be reverted.\n";

        return false;
    }
    */
}

<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\base\NotSupportedException;
use rmrevin\yii\fontawesome\component\Icon;
use rmrevin\yii\fontawesome\FAS;

/**
 * Class User
 * @package app\models
 *
 * @property integer $id
 * @property string $username
 * @property string $name
 * @property string $surname
 * @property string $position
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $role
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $password write-only password
 * @property string $newPassword
 *
 * @property string $fullName
 * @property Icon $statusIcon
 * @property string $statusText
 * @property array $statusesList
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_DISABLED = 0;
    const STATUS_ACTIVE = 1;

    const ROLE_USER = 'USER';
    const ROLE_ADMIN = 'ADMIN';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public $newPassword;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => Yii::$app->formatter->asDatetime(time(), 'php:Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['username', 'name', 'surname', 'position', 'email', 'role'], 'required', 'on' => [
                self::SCENARIO_CREATE, self::SCENARIO_UPDATE
            ]],
            [['newPassword'], 'required', 'on' => self::SCENARIO_CREATE],
            [['password_hash', 'auth_key', 'created_at', 'updated_at'], 'safe'],
            [['username', 'name', 'surname', 'position'], 'string'],
            [['email', 'username'], 'unique'],
            [['newPassword'], 'string', 'min' => 6],
            [['username'], 'string', 'min' => 6],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['role', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN]],

            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DISABLED]],
            ['role', 'default', 'value' => self::ROLE_USER],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'User name'),
            'name' => Yii::t('app', 'Name'),
            'surname' => Yii::t('app', 'Surname'),
            'position' => Yii::t('app', 'Position'),
            'auth_key' => Yii::t('app', 'Auth key'),
            'password_hash' => Yii::t('app', 'Password hash'),
            'email' => Yii::t('app', 'E-mail'),
            'role' => Yii::t('app', 'Role'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'password' => Yii::t('app', 'Password'),
            'fullName' => Yii::t('app', 'Full name'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        if (!empty($this->newPassword)) {
            $this->setPassword($this->newPassword);
            $this->generateAuthKey();
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by email
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->primaryKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     * @throws \yii\base\Exception
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Полное имя
     * @return string
     */
    public function getFullName()
    {
        return $this->name . ' ' . $this->surname;
    }

    /**
     * Иконка отображающая статус пользователя
     * @return \rmrevin\yii\fontawesome\component\Icon
     */
    public function getStatusIcon()
    {
        if (!$this->status) {
            return FAS::icon('times-circle', ['style' => 'color:#8b4747;']);
        }
        return FAS::icon('check-circle', ['style' => 'color:#478b74;', 'title' => $this->statusText]);
    }

    /**
     * Список статусов пользователя
     * @return array
     */
    public function getStatusesList()
    {
        return [
            $this::STATUS_DISABLED => Yii::t('app', 'Disabled'),
            $this::STATUS_ACTIVE => Yii::t('app', 'Active')
        ];
    }

    /**
     * Текстовое обозначение стуса пользователя
     * @return int|mixed
     */
    public function getStatusText()
    {
        return !empty($this->statusesList[$this->status]) ? $this->statusesList[$this->status] : $this->status;
    }
}

<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\base\NotSupportedException;

/**
 * Class User
 * @package app\models
 *
 * @property integer $id
 * @property string $username
 * @property string $name
 * @property string $surname
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property string $role
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const STATUS_DISABLED = 0;
    const STATUS_ACTIVE = 1;
    const ROLE_USER = 'USER';
    const ROLE_ADMIN = 'ADMIN';

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
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DISABLED]],
            ['role', 'default', 'value' => self::ROLE_USER],
            ['role', 'in', 'range' => [self::ROLE_USER, self::ROLE_ADMIN]]
        ];
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->primaryKey;
    }

    /**
     * {@inheritdoc}
     * @throws NotSupportedException
     */
    public function getAuthKey()
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        throw new NotSupportedException('"validateAuthKey" is not implemented.');
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
}

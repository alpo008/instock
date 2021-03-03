<?php

namespace app\modules\admin;

use Yii;
use app\models\User;
use yii\web\ForbiddenHttpException;

/**
 * admin module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\admin\controllers';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (!$this->checkAccessRights()) {
            throw new ForbiddenHttpException(Yii::t('app', 'Access denied'));
        }
    }

    /**
     * Проверка прав доступа к модулю администратора
     * @return bool
     */
    protected function checkAccessRights()
    {
        $access = false;
        if (!Yii::$app->user->isGuest && $user = Yii::$app->user->identity) {
            if ($user->role === User::ROLE_ADMIN) {
                $access = true;
            }
        }
        return $access;
    }
}

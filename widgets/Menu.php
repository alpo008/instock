<?php


namespace app\widgets;


use app\models\User;
use Yii;

/**
 * Class Menu
 * @package app\widgets
 */
class Menu extends \yii\widgets\Menu
{
    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->items = $this->items();
    }

    /**
     * @inheritdoc
     */
    protected function isItemActive($item)
    {
        if (!$this->activateItems) {
            return false;
        }
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $module = Yii::$app->controller->module->getUniqueId();
            $route = $item['url'][0];
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = $module . '/' . $route;
            }

            $route = ltrim($route, '/');

            if ($route === 'login' && $this->route === 'site/login') {
                return true;
            }
            if ($route === '' && $this->route === 'site/index') {
                return true;
            }
            if ($module === $route && strpos($this->route, 'default') !== false) {
                return true;
            }
            preg_match('/\/([^\/]+)\/?$/', $this->route, $matches);
            if (!empty($matches[0])) {
                if (substr($this->route, 0, strpos($this->route, $matches[0])) === $route) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /**
     * @return array|array[]
     */
    public function items ()
    {
        if (Yii::$app->user->isGuest) {
            return [
                ['label' => Yii::t('app', 'Login'), 'url' => ['/login']]
            ];
        } elseif (Yii::$app->user->identity->role === User::ROLE_USER) {
            return [
                ['label' => Yii::t('app', 'Main'), 'url' => ['/']]
            ];
        } elseif (Yii::$app->user->identity->role === User::ROLE_ADMIN) {
            return [
                ['label' => mb_strtoupper(Yii::t('app', 'Main')), 'url' => ['/admin']],
                ['label' => Yii::t('app', 'Users'), 'url' => ['/admin/user']],
                ['label' => Yii::t('app', 'Materials'), 'url' => ['/admin/material']],
                ['label' => Yii::t('app', 'Stock places'), 'url' => ['/admin/stock']],
                ['label' => Yii::t('app', 'Operations'), 'url' => ['/admin/stock-operation']]
            ];
        }
        return [];
    }
}
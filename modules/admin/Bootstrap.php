<?php

namespace app\modules\admin;

use yii\base\BootstrapInterface;
use yii\web\GroupUrlRule;

/**
 * Class Bootstrap
 * @package app\modules\admin
 *
 * @property string $urlPrefix
 * @property array $urlRules
 */
class Bootstrap implements BootstrapInterface
{

    public $urlPrefix = 'admin';

    /** @var array The rules to be used in URL management. */

    public $urlRules = [
        '<controller:\w+>/<id:\d+>' => '<controller>/view',
        '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>/<id:\d+>/<date>' => '<controller>/<action>',
        '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    ];
    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        $app->urlManager->addRules([
            new GroupUrlRule([
                "prefix" => $this->urlPrefix,
                "rules" => $this->urlRules,
            ]),
        ], false);
    }
}
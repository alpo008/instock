<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;
use app\models\Material;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $urgent = Material::find()->joinWith(['materialsStocks'])
            ->groupBy(['materials.ref'])
            ->joinWith('stocks')
            ->having('COALESCE(SUM({{%materials_stocks}}.qty), 0) <= COALESCE({{%materials}}.min_qty, 0)')
            ->count();
        return $this->render('index', compact('urgent'));
    }
}

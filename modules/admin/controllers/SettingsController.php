<?php


namespace app\modules\admin\controllers;


use app\custom\FileStorage;
use app\modules\admin\models\MaterialExport;
use yii\filters\VerbFilter;
use yii\web\Controller;

class SettingsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'quick-update' => ['POST'],
                ],
            ],
        ];
    }

    public function actionMaterialExportFormat()
    {
        $model = new MaterialExport();
        $columns = $model->columns();

        return $this->render('material-export-format-form', compact('model', 'columns'));
    }
}
<?php


namespace app\modules\admin\controllers;


use app\custom\FileStorage;
use app\models\Material;
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

    public function actionMaterialGroups()
    {
        $fileStorage = new FileStorage();
        $materialGroups = $fileStorage->getContent(Material::GROUPS_LIST_STORAGE);

        return $this->render('material-groups-form', compact('materialGroups'));
    }
}
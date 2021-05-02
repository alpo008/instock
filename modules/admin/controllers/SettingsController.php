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

    /**
     * @return string
     */
    public function actionMaterialGroups(): string
    {
        $fileStorage = new FileStorage();
        $materialGroups = $fileStorage->getContent(Material::GROUPS_LIST_STORAGE);
        if ($post = \Yii::$app->request->post()) {
            if (!empty($post['action']) && !empty($post['group']) && $post['action'] === 'delete') {
                if (array_key_exists($post['group'], $materialGroups)) {
                    unset($materialGroups[$post['group']]);
                    $fileStorage->setContent(Material::GROUPS_LIST_STORAGE, $materialGroups);
                }
            } elseif (!empty($post['materialGroups']) && is_array($post['materialGroups'])) {
                $updatedMaterialGroups = [];
                foreach ($post['materialGroups'] as $materialGroup) {
                    if (!array_key_exists($materialGroup, $updatedMaterialGroups) && !empty($materialGroup)) {
                        $updatedMaterialGroups[$materialGroup] = $materialGroup;
                    }
                }
                ksort($updatedMaterialGroups);
                $fileStorage->setContent(Material::GROUPS_LIST_STORAGE, $updatedMaterialGroups);
            }
        }
        $materialGroups = $fileStorage->getContent(Material::GROUPS_LIST_STORAGE);

        return $this->render('material-groups-form', compact('materialGroups'));
    }
}
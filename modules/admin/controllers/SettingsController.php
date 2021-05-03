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

    /**
     * @return string
     */
    public function actionMaterialTypes(): string
    {
        $fileStorage = new FileStorage();
        $materialTypes = $fileStorage->getContent(Material::TYPES_LIST_STORAGE);
        if ($post = \Yii::$app->request->post()) {
            if (!empty($post['action']) && !empty($post['group']) && $post['action'] === 'delete') {
                if (array_key_exists($post['group'], $materialTypes)) {
                    unset($materialTypes[$post['group']]);
                    $fileStorage->setContent(Material::TYPES_LIST_STORAGE, $materialTypes);
                }
            } elseif (!empty($post['materialTypes']) && is_array($post['materialTypes'])) {
                $updatedMaterialTypes = [];
                foreach ($post['materialTypes'] as $materialType) {
                    if (!array_key_exists($materialType, $updatedMaterialTypes) && !empty($materialType)) {
                        $updatedMaterialTypes[$materialType] = $materialType;
                    }
                }
                ksort($updatedMaterialTypes);
                $fileStorage->setContent(Material::TYPES_LIST_STORAGE, $updatedMaterialTypes);
            }
        }
        $materialTypes = $fileStorage->getContent(Material::TYPES_LIST_STORAGE);

        return $this->render('material-types-form', compact('materialTypes'));
    }
}
<?php


namespace app\modules\admin\controllers;


use Yii;
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
    public function behaviors(): array
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

    /**
     * @return string
     */
    public function actionBackup ()
    {
        $backupsPath = Yii::getAlias('@app/runtime/backups/');
        $dirs = scandir($backupsPath);
        $files = [];
        if (!empty($dirs) && is_array($dirs)) {
            foreach ($dirs as $dir) {
                if ($dir === '.' || $dir === '..') {
                    continue;
                }
                $dirContent = scandir($backupsPath . DIRECTORY_SEPARATOR . $dir);
                if (!empty($dirContent) && is_array($dirContent)) {
                    foreach ($dirContent as $entry) {
                        if ($entry === '.' || $entry === '..') {
                            continue;
                        }
                        if (!$fileDate = $this->getDate($entry)) {
                            continue;
                        }
                        $files[$fileDate] = $entry;
                    }
                }
            }
        }
        if ($fileName = Yii::$app->request->post('file')) {
            $path = $backupsPath . str_replace(['instock_db_backup_', '.sql'], '', $fileName) .
                DIRECTORY_SEPARATOR . $fileName;
            if (is_file($path)) {
                Yii::$app->response->sendFile($path);
            }
        }
        return $this->render('backups', compact('files'));
    }

    /**
     * @param string $filename
     * @return string
     */
    private function getDate (string $filename) :string
    {
        $rawDate = str_replace(['instock_db_backup_', '.sql'], '', $filename);
        if ($dateTime = \DateTime::createFromFormat('Ymd_His', $rawDate)) {
            $tzSeconds = Yii::$app->params['timeZoneShift'] ?? 0;
            return  $dateTime->modify('+ ' . $tzSeconds . 'second')->format('d.m.Y H:i:s');
        }
        return '';
    }
}
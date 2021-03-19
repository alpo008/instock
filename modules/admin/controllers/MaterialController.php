<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Material;
use app\modules\admin\models\MaterialSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * MaterialController implements the CRUD actions for Material model.
 */
class MaterialController extends Controller
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

    /**
     * Lists all Material models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MaterialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Material model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Material model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Material();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Material model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @param integer $id
     * @return array
     */
    public function actionQuickUpdate($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $errors = [];
        $newValue = '';
        try {
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post())) {
                if (!empty($model->dirtyAttributes)) {
                    $newValue = ($model->dirtyAttributes[array_key_first($model->dirtyAttributes)]);
                }
                if (!$model->save()) {
                    $newValue = '';
                    $errors = $model->firstErrors;
                }
            } else {
                $errors = ['generic' => Yii::t('app', 'Bad parameters')];
            }
        } catch (NotFoundHttpException $e) {
            $errors = ['generic' => Yii::t('app', 'Material not found')];
        }
        return compact('errors', 'newValue');
    }

    /**
     * Deletes an existing Material model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Material model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Material the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Material::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

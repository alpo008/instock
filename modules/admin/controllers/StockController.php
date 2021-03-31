<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\Stock;
use app\modules\admin\models\StockSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * StockController implements the CRUD actions for Stock model.
 */
class StockController extends Controller
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
     * Lists all Stock models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Stock model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        try {
            $model = $this->findModel($id);
            $materials = $model->materials;
        } catch (NotFoundHttpException $e) {
            $materials = null;
        }
        return $this->render('view', compact('model', 'materials'));
    }

    /**
     * Creates a new Stock model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Stock();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Stock model.
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
            $errors = ['generic' => Yii::t('app', 'Stock place not found')];
        }
        return compact('errors', 'newValue');
    }

    /**
     * Deletes an existing Stock model.
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
     * Finds the Stock model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stock the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stock::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

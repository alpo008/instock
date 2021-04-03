<?php

namespace app\modules\admin\controllers;

use Yii;
use app\models\StockOperation;
use app\modules\admin\models\StockOperationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * StockOperationController implements the CRUD actions for StockOperation model.
 */
class StockOperationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    //'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all StockOperation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StockOperationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StockOperation model.
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
     * Creates a new Debit StockOperation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $materialId
     * @param string $stockId
     * @return mixed
     */
    public function actionCreateDebit($materialId = '0', $stockId = '0')
    {
        $model = new StockOperation();
        $model->operation_type = $model::DEBIT_OPERATION;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', compact(
            'model', 'materialId', 'stockId')
        );
    }

    /**
     * Creates a new Credit StockOperation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $materialId
     * @param string $stockId
     * @return mixed
     */
    public function actionCreateCredit($materialId = '0', $stockId = '0')
    {
        $model = new StockOperation();
        $model->operation_type = $model::CREDIT_OPERATION;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', compact(
                'model', 'materialId', 'stockId')
        );
    }

    /**
     * Creates a new Correction StockOperation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @param string $materialId
     * @param string $stockId
     * @return mixed
     */
    public function actionCreateCorrection($materialId = '0', $stockId = '0')
    {
        $model = new StockOperation();
        $model->operation_type = $model::CORRECTION_OPERATION;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', compact(
                'model', 'materialId', 'stockId')
        );
    }

    /**
     * Finds the StockOperation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StockOperation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StockOperation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}

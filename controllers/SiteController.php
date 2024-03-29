<?php

namespace app\controllers;

use Yii;
use app\models\Material;
use app\models\searchModels\MaterialSearch;
use app\models\StockOperation;
use app\models\User;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'logout', 'credit', 'validate-form'],
                'rules' => [
                    [
                        'actions' => ['index', 'logout', 'credit', 'validate-form'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'validate-form' => ['post'],
                    'credit' => ['post'],
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string|Response
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        if ($user instanceof User && $user->role === User::ROLE_ADMIN) {
            return $this->response->redirect(['/admin']);
        }
        $material = null;
        $stockOperation = null;
        if ($id = Yii::$app->request->post('id')) {
            $material = Material::findOne($id);
            $stock_id = Yii::$app->request->post('stock_id');
            if (empty($stock_id) && !empty($material->stocks[0])) {
                $stock_id = $material->stocks[0]->id;
            }
            $stockOperation = new StockOperation([
                'operation_type' => StockOperation::CREDIT_OPERATION,
                'material_id' => $id,
                'stock_id' => $stock_id
            ]);
        }
        $searchModel = new MaterialSearch();
        $queryParams = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($queryParams);

        return $this->render('index', compact(
            'searchModel', 'dataProvider', 'material', 'stockOperation'));
    }

    /**
     * @return array
     */
    public function actionCredit ()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $status = 400;
        $code = 'NOK';
        $message = Yii::t('app', 'Operation error. Please contact administrator');
        $model = new StockOperation([
            'operation_type' => StockOperation::CREDIT_OPERATION,
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $status = 200;
            $code = 'OK';
            $message = Yii::t('app', 'From stock place {stock} were taken {qty} of material {material}',
                [
                    'stock' => $model->stock->alias,
                    'qty' => $model->qty . ' ' . $model->material->unitName,
                    'material' => $model->material->name
                ]
            );
        }
        return compact('status', 'code', 'message');
    }

    /**
     * Метод для Ajax-валидации формы
     * @return array
     */
    public function actionValidateForm ()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $model = new StockOperation(['operation_type' => StockOperation::CREDIT_OPERATION]);
        $model->load(Yii::$app->request->post());
        $model->validate();
        foreach ($model->errors as $attribute => $errors) {
            $result[Html::getInputId($model, $attribute)] = $errors;
        }
        return $result;
    }

    /**
     * Login action
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $user = Yii::$app->user->identity;
        if ($user instanceof User) {
            if ($user->role === User::ROLE_USER) {
                return $this->response->redirect(['/']);
            } elseif ($user->role === User::ROLE_ADMIN) {
                return $this->response->redirect(['/admin']);
            }
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $user = Yii::$app->user->identity;
            if ($user instanceof User) {
                if ($user->role === User::ROLE_USER) {
                    return $this->response->redirect(['/']);
                } elseif ($user->role === User::ROLE_ADMIN) {
                    return $this->response->redirect(['/admin']);
                }
            }
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}

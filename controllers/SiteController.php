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
                'only' => ['index', 'logout'],
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
     * @return string
     */
    public function actionIndex()
    {
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
     *
     */
    public function actionCredit ()
    {

    }

    /**
     * Метод для Ajax-валидации формы
     * @return array
     */
    public function actionValidateForm ()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];
        $model = new StockOperation();
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

<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <div class="action-buttons">
    <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(FAS::icon('user-plus'), ['create'],
            ['class' => 'btn btn-success', 'title' => Yii::t('app', 'Add user')]) ?>
    </div>

    <?php Pjax::begin(['id' => 'user-index-pjax-container']); ?>

    <?= GridView::widget([
        'id' => 'user-index-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'fullName',
                'format' => 'raw',
                'value' => function($model) {
                    /** @var $model \app\models\User */
                    return Html::a($model->fullName, ['view', 'id' => $model->id]);
                }
            ],
            'position',
            'email:email',
            [
                'attribute' => 'username',
                'label' => Yii::t('app', 'login')
            ],
            [
                'attribute' => 'role',
                'filter' => ['USER' => 'USER', 'ADMIN' => 'ADMIN'],
                //'filterOptions' => ['style' => 'min-width: 120px;']
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    /* @var $model \app\models\User */
                    return $model->statusIcon;
                },
                'filter' => $searchModel->statusesList,
                //'filterOptions' => ['style' => 'min-width: 160px;']
            ],
            [
                'attribute' => 'updated_at',
                'label' => Yii::t('app', 'Changed'),
                'value' => function($model) {
                    /** @var $model \app\models\User */
                    return Yii::$app->formatter->asDate($model->updated_at);
                },
            ]
        ]
    ]); ?>

    <?php Pjax::end(); ?>

</div>

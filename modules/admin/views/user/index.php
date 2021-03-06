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
            'username',
            [
                'attribute' => 'role',
                'filter' => ['USER' => 'USER', 'ADMIN' => 'ADMIN']
            ],
            //'status',
            //'created_at',
            [
                'attribute' => 'updated_at',
                'value' => function($model) {
                    /** @var $model \app\models\User */
                    return Yii::$app->formatter->asDate($model->updated_at);
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

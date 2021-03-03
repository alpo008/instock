<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Add user'), ['create'], ['class' => 'btn btn-add']) ?>
    </p>

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
            'role',
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

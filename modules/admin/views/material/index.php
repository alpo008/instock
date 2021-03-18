<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\MaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Materials');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-index">

    <div class="action-buttons">
    <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(FAS::icon('layer-plus'), ['create'], [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Add material')
        ]) ?>
    </div>

    <?php Pjax::begin(['id' => 'material-index-pjax-container']); ?>

    <?= GridView::widget([
        'id' => 'material-index-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'ref',
                'format' => 'raw',
                'value' => function($model) {
                    /* @var $model \app\models\Material */
                    return Html::a($model->ref, ['view', 'id' => $model->id]);
                }
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (\app\models\Material $model) {
                    $value = $model->name;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/material/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textarea('Material[name]', $value),
                        'formId' => 'name-update-form-' . $model->id,
                        'containerId' => 'cell-editable-' . $model->id,
                        'successCallback' => null,
                        'errorCallback' => 'showCommonAlert',
                    ]);
                },
                'contentOptions' => function(\app\models\Material $model) {
                    return [
                        'id' => 'cell-editable-' . $model->id,
                        'class' => 'cell-editable'
                    ];
                }
            ],
            'qty',
            'min_qty',
            'max_qty',
            [
                'attribute' => 'unit',
                'value' => function($model) {
                    /* @var $model \app\models\Material */
                    return $model->unitName;
                },
                'filter' => $searchModel->unitsList
            ],
            //'type',
            //'group',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

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

        <?= Html::a(FAS::icon('file-export'), ['export'], [
            'class' => 'btn btn-primary',
            'title' => Yii::t('app', 'Export table to Excel')
        ]) ?>

    </div>

    <?php Pjax::begin(['id' => 'material-index-pjax-container']); ?>

    <?= GridView::widget([
        'id' => 'material-index-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'ref',
                'format' => 'raw',
                'value' => function (\app\models\Material $model) {
                    $value = $model->ref;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/material/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textInput('Material[ref]', $value, [
                            'class' => 'cell-editable__input',
                            'autocomplete' => 'off'
                        ]),
                    ]);
                },
                'contentOptions' =>  ['class' => 'cell-editable'],
            ],
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function (\app\models\Material $model) {
                    $value = $model->name;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/material/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textInput('Material[name]', $value, [
                            'class' => 'cell-editable__input',
                            'autocomplete' => 'off'
                        ]),
                    ]);
                },
                'contentOptions' => ['class' => 'cell-editable'],
            ],
            'qty',
            [
                'attribute' => 'min_qty',
                'format' => 'raw',
                'value' => function (\app\models\Material $model) {
                    $value = $model->min_qty;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/material/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textInput('Material[min_qty]', $value, [
                            'class' => 'cell-editable__input',
                            'type' => 'number',
                            'min' => 0,
                            'max' => $model->max_qty ? $model->max_qty : 1
                        ]),
                    ]);
                },
                'contentOptions' => ['class' => 'cell-editable'],
            ],
            [
                'attribute' => 'max_qty',
                'format' => 'raw',
                'value' => function (\app\models\Material $model) {
                    $value = $model->max_qty;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/material/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textInput('Material[max_qty]', $value, [
                            'class' => 'cell-editable__input',
                            'type' => 'number',
                            'min' => $model->min_qty ? $model->min_qty : 0
                        ]),
                    ]);
                },
                'contentOptions' => ['class' => 'cell-editable'],
            ],
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

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        /** @var $model \app\models\Material */
                        return Html::a(
                            FAS::icon('eye'),
                            ['material/view', 'id' => $model->id],
                            [
                                'title' => Yii::t('app', 'View')
                            ]
                        );
                    },
                    'update' => function ($url, $model, $key) {
                        /** @var $model \app\models\Material */
                        return Html::a(
                            FAS::icon('edit'),
                            ['material/update', 'id' => $model->id],
                            [
                                'title' => Yii::t('app', 'Edit')
                            ]
                        );
                    },
                    'delete' => function ($url, $model, $key) {
                        /** @var $model \app\models\Material */
                        return Html::a(
                            FAS::icon('trash-alt'),
                            ['material/delete', 'id' => $model->id],
                            [
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                                'title' => Yii::t('app', 'Delete material')
                            ]
                        );
                    }
                ]
            ]
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

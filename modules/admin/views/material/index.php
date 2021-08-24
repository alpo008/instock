<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\MaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $importModel \app\modules\admin\models\MaterialImport */

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

        <?= Html::button(FAS::icon('file-import'), [
        'type' => 'button',
        'class' => 'btn btn-danger',
        'title' => Yii::t('app', 'Import materials from file'),
        'data' => [
            'toggle' => 'modal',
            'target' => '#materialImportModal'
        ]
        ]) ?>

    </div>

    <?php Pjax::begin(['id' => 'material-index-pjax-container', 'enablePushState' => true]); ?>

    <?= GridView::widget([
        'id' => 'material-index-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => Yii::t('app', 'Photo'),
                'format' => 'raw',
                'value' => function (\app\models\Material $model) {
                    if (!empty($model->photoPath)) {
                        return Html::img($model->photoPath, ['class' => 'img-material in-grid']) .
                            Html::beginTag('div', ['class' => 'material-photo-popup']) .
                            Html::img($model->photoPath) .
                            Html::endTag('div')
                            ;
                    } else {
                        return null;
                    }
                }
            ],
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
                'contentOptions' => ['class' => 'cell-editable']
            ],
            [
                'attribute' => 'type',
                'filter' => $searchModel->typesList,
            ],
            [
                'attribute' => 'group',
                'filter' => $searchModel->groupsList,
            ],
            [
                'attribute' => 'quantity',
                'value' => function($model) { /* @var $model \app\models\Material */
                    return $model->quantity + 0;
                },
                'filter' => $searchModel->quantityFilter,
                'contentOptions' => ['class' => 'cell-quantity']
            ],
            [
                'attribute' => 'min_qty',
                'format' => 'raw',
                'value' => function (\app\models\Material $model) {
                    $value = $model->min_qty + 0;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/material/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textInput('Material[min_qty]', $value, [
                            'class' => 'cell-editable__input',
                            'type' => 'number',
                            'min' => 0,
                            'autocomplete'=> 'off'
                        ]),
                    ]);
                },
                'contentOptions' => ['class' => 'cell-editable'],
            ],
            [
                'attribute' => 'max_qty',
                'format' => 'raw',
                'value' => function (\app\models\Material $model) {
                    $value = $model->max_qty + 0;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/material/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textInput('Material[max_qty]', $value, [
                            'class' => 'cell-editable__input',
                            'type' => 'number',
                            'min' => 0,
                            'autocomplete'=> 'off'
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
            [
                'attribute' => 'stockAliases',
                'format' => 'raw',
                'value' => function($model) {
                    /* @var $model \app\models\Material */
                    $result = '';
                    foreach ($model->stockAliases as $id => $alias) {
                        $result .= Html::a($alias, ['/admin/stock', 'id' => $id] , [
                            'title' => $model->getQuantity($id) . ' ' . $model->unitName
                        ]) . ', ';
                    }
                    return rtrim($result, ', ');
                },
                'filter' => $searchModel->stockAliasesFilter
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
                                'title' => Yii::t('app', 'Details')
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
                        if (!empty($model->materialsStocks)) {
                            return false;
                        }
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
                    },
                    'move' => function ($url, $model, $key) {
                        /** @var $model \app\models\Material */
                        if (empty($model->stocks)) {
                            return false;
                        }
                        $stockId = 0;
                        if (is_array($model->stocks) && count($model->stocks) === 1) {
                            $stockId = $model->stocks[0]->id;
                        }
                        return Html::a(
                            FAS::icon('person-carry'),
                            ['material/move', 'material_id' => $model->id, 'stock_id' => $stockId],
                            [
                                'title' => Yii::t('app', 'Move material')
                            ]
                        );
                    },
                    'debit' => function ($url, $model, $key) {
                        /** @var $model \app\models\Material */
                        $stockId = '0';
                        if (is_array($model->stocks) && count($model->stocks) === 1) {
                            $stockId = $model->stocks[0]->id;
                        }
                        return Html::a('<span class="before-icon">+</span>' . FAS::icon('file-invoice'), [
                            'stock-operation/create-debit',
                            'material_id' => $model->id,
                            'stock_id' => $stockId
                        ], [
                            'style' => 'position:relative;',
                            'title' => Yii::t('app', 'Create debit operation')
                        ]);
                    },
                    'credit' => function ($url, $model, $key) {
                        /** @var $model \app\models\Material */
                        if (empty($model->stocks)) {
                            return false;
                        }
                        $stockId = '0';
                        if (is_array($model->stocks) && count($model->stocks) === 1) {
                            $stockId = $model->stocks[0]->id;
                        }
                        return Html::a('<span class="before-icon">-</span>' . FAS::icon('file-invoice'), [
                            'stock-operation/create-credit',
                            'material_id' => $model->id,
                            'stock_id' => $stockId
                        ], [
                            'style' => 'position:relative;',
                            'title' => Yii::t('app', 'Create credit operation')
                        ]);
                    }
                ],
                'template' => '{view} {update} {debit} {credit} {move} {delete}'
            ]
        ],
        'rowOptions' => function ($model, $index, $widget, $grid){
            /** @var $model \app\models\Material */
            if ($model->quantity <= $model->min_qty) {
                return ['class' => 'low-quantity'];
            } elseif ($model->quantity > $model->max_qty) {
                return ['class' => 'extra-quantity'];
            } else {
                return [];
            }
        }
    ]); ?>

    <?php Pjax::end(); ?>
</div>

<?= $this->render('_import_form', ['model' => $importModel]) ?>

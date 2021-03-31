<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FAS;


/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\StockSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stock places');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-index">

    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a('<span class="before-icon">+</span>' . FAS::icon('inventory'), ['create'], [
            'class' => 'btn btn-success',
            'style' => 'position:relative;',
            'title' => Yii::t('app', 'Add stock place')
        ]) ?>
    </div>
    <?php Pjax::begin(['id' => 'stock-index-pjax-container']); ?>

    <?= GridView::widget([
        'id' => 'stock-index-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'alias',
                'format' => 'raw',
                'value' => function (\app\models\Stock $model) {
                    $value = $model->alias;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/stock/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textInput('Stock[alias]', $value, [
                            'class' => 'cell-editable__input',
                            'autocomplete' => 'off'
                        ]),
                    ]);
                },
                'contentOptions' =>  ['class' => 'cell-editable'],
            ],
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function (\app\models\Stock $model) {
                    $value = $model->description;
                    return $this->render('@app/views/_components/_cell_editable', [
                        'value' => $value,
                        'url' => Url::to(['/admin/stock/quick-update/', 'id' =>  $model->id]),
                        'input' => Html::textarea('Stock[description]', $value, [
                            'class' => 'cell-editable__input',
                            'autocomplete' => 'off'
                        ]),
                    ]);
                },
                'contentOptions' =>  ['class' => 'cell-editable'],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        /** @var $model \app\models\Stock */
                        return Html::a(
                            FAS::icon('eye'),
                            ['stock/view', 'id' => $model->id],
                            [
                                'title' => Yii::t('app', 'Details')
                            ]
                        );
                    },
                    'update' => function ($url, $model, $key) {
                        /** @var $model \app\models\Stock */
                        return Html::a(
                            FAS::icon('edit'),
                            ['stock/update', 'id' => $model->id],
                            [
                                'title' => Yii::t('app', 'Edit')
                            ]
                        );
                    },
                    'delete' => function ($url, $model, $key) {
                        /** @var $model \app\models\Stock */
                        if (!empty($model->materialsStocks)) {
                            return false;
                        }
                        return Html::a(
                            FAS::icon('trash-alt'),
                            ['stock/delete', 'id' => $model->id],
                            [
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                                    'method' => 'post',
                                ],
                                'title' => Yii::t('app', 'Delete stock place')
                            ]
                        );
                    }
                ]
            ]
        ]
    ]); ?>

    <?php Pjax::end(); ?>

</div>

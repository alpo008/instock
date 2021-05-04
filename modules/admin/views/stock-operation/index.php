<?php


use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\StockOperationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stock operations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-operation-index">

    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>
        <?= Html::a(FAS::icon('file-export'), ['export'], [
            'class' => 'btn btn-primary',
            'title' => Yii::t('app', 'Export table to Excel')
        ]) ?>
    </div>

    <?php Pjax::begin(['id' => 'stock-operations-index-pjax-container']); ?>

    <?= GridView::widget([
        'id' => 'stock-operations-index-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var $model \app\models\StockOperation */
                    return Html::a($model->created_at, ['/admin/stock-operation/' . $model->id], [
                        'title' => Yii::t('app', 'Operation details')
                    ]);
                }
            ],
            [
                'attribute' => 'materialRef',
                'format' => 'raw',
                'value' => function ($model) {
                    /* @var $model \app\models\StockOperation */
                    return Html::a($model->materialRef, ['/admin/material/' . $model->material_id], [
                        'title' => Yii::t('app', 'View material')
                    ]);
                }
            ],
            'materialName',
            [
                'attribute' => 'stockAlias',
                'filter' => \app\models\Stock::getStocksList()
            ],
            [
                'attribute' => 'operationType',
                'filter' => $searchModel::getOperationTypes()
            ],
            'qty',
            //'from_to',
            //'comments:ntext',
            //'created_at',
            //'created_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

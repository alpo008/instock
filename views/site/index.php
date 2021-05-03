<?php

/* @var $this yii\web\View */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModel \app\models\searchModels\MaterialSearch */
/* @var $material \app\models\Material | null */
/* @var $stockOperation \app\models\StockOperation | null */

use yii\bootstrap4\Alert;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Inspection stock');

$this->registerJs(
    file_get_contents(Yii::getAlias('@app/views/site/') . '_form.js'),
    $this::POS_END
);

?>
<div class="material-index" style="padding-top:1rem;">
    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php Pjax::begin(['id' => 'site-index-pjax-container', 'enablePushState' => true]); ?>

    <div id="operation-alert" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
        <p id="alert-message"></p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <?= GridView::widget([
        'id' => 'site-index-grid-view',
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
                            Html::img($model->photoPath, /*['width' => 400]*/) .
                            Html::endTag('div')
                            ;
                    } else {
                        return null;
                    }
                }
            ],
            'ref',
            'name',
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
                'contentOptions' => ['class' => 'cell-quantity']
            ],
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
            [
                'attribute' => 'stockAliases',
                'format' => 'raw',
                'value' => function($model) {
                    /* @var $model \app\models\Material */
                    $result = '';
                    foreach ($model->stockAliases as $id => $alias) {
                        $quantity = round($model->getQuantity($id), 2);
                        $result .= Html::a($alias,
                                Yii::$app->request->url, [
                            'title' => Yii::t('app', 'Take') . ' ' . $model->shortName .
                                ' ' . Yii::t('app', 'from cell') . ' ' . $alias .
                                ' ( ' . Yii::t('app', 'rest') .
                                $quantity . ' ' . $model->unitName . ' )',
                            'data' => [
                                'method' => 'post',
                                'pjax' => '1',
                                'params' => ['id' => $model->id, 'stock_id' => $id]
                            ]
                        ]) . '<br>';
                    }
                    return rtrim($result, ', ');
                },
                'filter' => $searchModel->stockAliasesFilter
            ]
         ],
        'rowOptions' => function ($model, $index, $widget, $grid){
            /** @var $model \app\models\Material */
            return $model->quantity <= $model->min_qty ? ['class' => 'low-quantity'] : [];
        }
    ]);
    ?>

    <?php if ($material instanceof \app\models\Material) : ?>
        <?= $this->render('_form', compact('material', 'stockOperation')) ?>
    <?php endif; ?>

    <?php Pjax::end(); ?>
</div>

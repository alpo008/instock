<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\StockOperation */

$this->title = $model->operationTime;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock operations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="stock-operation-view">

    <div class="action-buttons">
        <h1>
            <?= Html::encode($model->operationType . ' ' .$model->operationTime . ', ' . $model->materialName) ?>
        </h1>
    </div>

    <?= DetailView::widget([
        'id' => 'stock-operation-detail-view',
        'model' => $model,
        'options' => ['class' => 'table detail-view'],
        'attributes' => [
            'id',
            'creatorName',
            'stockAlias',
            'operationType',
            'qty',
            [
                'attribute' => 'from_to',
                'label' => $model->operation_type === $model::CORRECTION_OPERATION ?
                    Yii::t('app', 'Correction reason') : (
                        $model->operation_type === $model::CREDIT_OPERATION ?
                            Yii::t('app', 'Material destination') :
                            Yii::t('app', 'Material source')
                    )
            ],

            'comments:ntext'
            //'created_at',
            //'created_by',
        ]
    ]) ?>

</div>

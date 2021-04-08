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
            <?= Html::encode($model->operationType . ' ' .$model->operationTime . ', ' . $model->creatorName) ?>
        </h1>
    </div>

    <?= DetailView::widget([
        'id' => 'stock-operation-detail-view',
        'model' => $model,
        'attributes' => [
            'id',
            'materialName',
            'stockAlias',
            'operationType',
            'qty',
            'from_to',
            'comments:ntext'
            //'created_at',
            //'created_by',
        ]
    ]) ?>

</div>

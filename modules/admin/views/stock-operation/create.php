<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StockOperation */

switch ($model->operation_type) {
    case $model::CREDIT_OPERATION:
        $this->title = Yii::t('app', 'Credit operation');
    break;
    case $model::DEBIT_OPERATION:
        $this->title = Yii::t('app', 'Debit operation');
    break;
    case $model::CORRECTION_OPERATION:
        $this->title = Yii::t('app', 'Correction operation');
    break;
}

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock operations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-operation-create">

    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?php if ($model->material instanceof \app\models\Material) : ?>
        <div class="material-name">
            <h2>
                <?= $model->materialRef . ' ; ' . $model->materialName ?>
            </h2>
        </div>
    <?php endif; ?>

    <?= $this->render('_form', compact('model')) ?>

</div>

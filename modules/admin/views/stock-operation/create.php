<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\StockOperation */

switch ($model->operation_type) {
    case $model::CREDIT_OPERATION:
        $this->title = Yii::t('app', 'Credit operation');
        $available = !empty($model->stock) ?
            $model->material->getQuantity($model->stock_id) :
            $model->material->quantity;
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
            <table class="table">
                <tr>
                    <td>
                        <?= Yii::t('app', 'Material') ?>
                    </td>
                    <td>
                        <?= $model->material->name ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= Yii::t('app', 'Qty') ?>
                    </td>
                    <td>
                        <?= $model->material->quantity . ' ' . $model->material->unitName ?>
                    </td>
                </tr>
                <?php if (!empty($available)) : ?>
                    <tr>
                        <td>
                            <?= Yii::t('app', 'Available at') . ' ' .
                                Html::tag('span', $model->stockAlias, ['id' => 'stock-alias'])
                            ?>
                        </td>
                        <td id="material-available-qty">
                            <span id="stock-qty">
                                <?= $model->material->getQuantity($model->stock_id) ?>
                            </span>
                            &nbsp;
                            <?= $model->material->unitName ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
            <hr>
        </div>
    <?php endif; ?>

    <?= $this->render('_form', compact('model')) ?>

    <?= Html::tag('div', null, [
        'id' => 'stock-quantities',
        'style' => 'display:none;',
        'data' => ['quantities' => $model->materialAvailability]
    ]) ?>

</div>

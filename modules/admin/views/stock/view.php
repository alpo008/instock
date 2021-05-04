<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */
/* @var $materials \app\models\Material[] | null */

$this->title = $model->alias;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock places'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="stock-view">

    <div class="action-buttons">

        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(FAS::icon('edit'), ['update', 'id' => $model->id], [
            'class' => 'btn btn-primary',
            'title' => Yii::t('app', 'Edit stock place')
        ]) ?>
        <?= Html::a('<span class="before-icon">+</span>' . FAS::icon('file-invoice'), [
            'stock-operation/create-debit',
            'stock_id' => $model->id
        ], [
            'class' => 'btn btn-success',
            'style' => 'position:relative;',
            'title' => Yii::t('app', 'Create debit operation')
        ]) ?>
        <?php if (empty($model->materialsStocks)) : ?>
            <?= Html::a(FAS::icon('trash-alt'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'title' => Yii::t('app', 'Delete stock place'),
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </div>

    <?= DetailView::widget([
        'id' => 'stock-detail-view',
        'model' => $model,
        'attributes' => [
            'alias',
            'description:ntext',
        ],
    ]) ?>

    <?php if (!empty($materials) && is_array($materials)) : ?>
        <div class="table-caption">
            <?= Yii::t('app', 'List of materials') ?>
        </div>
        <table class="table relations-list">
            <?php foreach ($materials as $material): ?>
                <tr>
                    <td>
                        <?= \yii\bootstrap4\Html::a($material->ref, ['material/view', 'id' => $material->id], [
                            'title' => Yii::t('app', 'View') . ' ' . $material->name
                        ]) ?>
                    </td>
                    <td>
                        <?= $material->name ?>
                    </td>
                    <td>
                        <?= $material->getQuantity($model->id) . ' ' . $material->unitName ?>
                    </td>
                    <td class="operations-links">
                        <?= Html::a('<span class="before-icon">+</span>' . FAS::icon('file-invoice'), [
                            'stock-operation/create-debit',
                            'material_id' => $material->id,
                            'stock_id' => $model->id
                        ], [
                            'style' => 'position:relative;',
                            'title' => Yii::t('app', 'Create debit operation')
                        ]) ?>
                        <?php if ($model->materialsStocks) : ?>
                            <?= Html::a('<span class="before-icon">-</span>' . FAS::icon('file-invoice'), [
                                'stock-operation/create-credit',
                                'material_id' => $material->id,
                                'stock_id' => $model->id
                            ], [
                                'style' => 'position:relative;',
                                'title' => Yii::t('app', 'Create credit operation')
                            ]) ?>
                            <?= Html::a(FAS::icon('ballot-check'), [
                                'stock-operation/create-correction',
                                'material_id' => $material->id,
                                'stock_id' => $model->id
                            ], [
                                'style' => 'position:relative;',
                                'title' => Yii::t('app', 'Create correction operation')
                            ]) ?>
                        <?php endif; ?>
                        <?= Html::a(
                            FAS::icon('person-carry'),
                            ['material/move', 'material_id' => $material->id, 'stock_id' => $model->id],
                            [
                                'title' => Yii::t('app', 'Move material')
                            ]
                        ); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

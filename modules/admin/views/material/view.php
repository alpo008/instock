<?php

use app\models\User;
use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Material */
/* @var $stocks \app\models\Stock[] | null */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="material-view">

    <div class="action-buttons">
        <?php
            if (!empty($model->photoPath)) {
                echo Html::img($model->photoPath, ['width' => '200', 'class' => 'img-material']);
            }
        ?>
        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(FAS::icon('edit'), ['update', 'id' => $model->id], [
            'class' => 'btn btn-primary',
            'title' => Yii::t('app', 'Edit material card')
        ]) ?>
        <?= Html::a('<span class="before-icon">+</span>' . FAS::icon('file-invoice'), [
            'stock-operation/create-debit',
            'material_id' => $model->id
        ], [
            'class' => 'btn btn-success',
            'style' => 'position:relative;',
            'title' => Yii::t('app', 'Create debit operation')
        ]) ?>
        <?php if (empty($model->materialsStocks)) : ?>
            <?= Html::a(FAS::icon('trash-alt'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'title' => Yii::t('app', 'Delete material'),
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </div>


    <?= DetailView::widget([
        'id' => 'material-detail-view',
        'model' => $model,
        'options' => ['class' => 'table detail-view'],
        'attributes' => [
            'ref',
            'quantity',
            'min_qty',
            'max_qty',
            [
                'attribute' => 'unit',
                'value' => $model->unitName
            ],
            'type',
            'group',
            [
                'label' => Yii::t('app', 'Addition'),
                'value' => $model->created_at . ' , ' .
                    ($model->creator instanceof User ? $model->creator->fullName :
                    Yii::t('app', 'Not set'))
            ],
            [
                'label' => Yii::t('app', 'Last edition'),
                'value' => $model->updated_at . ' , ' .
                    ($model->editor instanceof User ? $model->editor->fullName :
                    Yii::t('app', 'Not set'))
            ]
        ],
    ]) ?>

    <?php if (!empty($stocks) && is_array($stocks)) : ?>
        <div class="table-caption">
            <?= Yii::t('app', 'Stock places') ?>
        </div>
        <table class="table relations-list">
            <?php foreach ($stocks as $stock): ?>
                <tr>
                    <td>
                        <?= \yii\bootstrap4\Html::a($stock->alias, ['stock/view', 'id' => $stock->id], [
                            'title' => Yii::t('app', 'View') . ' ' . $stock->alias
                        ]) ?>
                    </td>
                    <td>
                        <?= $model->getQuantity($stock->id) ?>
                    </td>
                    <td class="operations-links">
                        <?= Html::a('<span class="before-icon">+</span>' . FAS::icon('file-invoice'), [
                            'stock-operation/create-debit',
                            'material_id' => $model->id,
                            'stock_id' => $stock->id
                        ], [
                            'style' => 'position:relative;',
                            'title' => Yii::t('app', 'Create debit operation')
                        ]) ?>
                        <?php if ($model->materialsStocks) : ?>
                            <?= Html::a('<span class="before-icon">-</span>' . FAS::icon('file-invoice'), [
                                'stock-operation/create-credit',
                                'material_id' => $model->id,
                                'stock_id' => $stock->id
                            ], [
                                'style' => 'position:relative;',
                                'title' => Yii::t('app', 'Create credit operation')
                            ]) ?>
                            <?= Html::a(FAS::icon('ballot-check'), [
                                    'stock-operation/create-correction',
                                    'material_id' => $model->id,
                                    'stock_id' => $stock->id
                            ], [
                                'style' => 'position:relative;',
                                'title' => Yii::t('app', 'Create correction operation')
                            ]) ?>
                        <?php endif; ?>
                        <?= Html::a(
                            FAS::icon('person-carry'),
                            ['material/move', 'material_id' => $model->id, 'stock_id' => $stock->id],
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

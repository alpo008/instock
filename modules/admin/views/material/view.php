<?php

use app\models\User;
use rmrevin\yii\fontawesome\FAS;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Material */

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
        <?= Html::a(FAS::icon('trash-alt'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'title' => Yii::t('app', 'Delete material'),
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>


    <?= DetailView::widget([
        'id' => 'material-detail-view',
        'model' => $model,
        'options' => ['class' => 'table detail-view'],
        'attributes' => [
            'ref',
            'qty',
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

</div>

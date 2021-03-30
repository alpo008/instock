<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */

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
        <?php //TODO CHECK Material on place ?>
        <?= Html::a(FAS::icon('trash-alt'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'title' => Yii::t('app', 'Delete stock place'),
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'alias',
            'description:ntext',
        ],
    ]) ?>

</div>

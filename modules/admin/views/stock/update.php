<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */

$this->title = Yii::t('app', 'Update stock: {name}', [
    'name' => $model->alias,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock places'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->alias, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="stock-update">

    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

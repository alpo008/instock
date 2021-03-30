<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */

$this->title = Yii::t('app', 'Stock place addition');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stock places'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-create">

    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>

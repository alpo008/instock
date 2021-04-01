<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\MaterialMovementForm */
/* @var $form yii\bootstrap4\ActiveForm */

$this->title = Yii::t('app', 'Material movement').': '. $model->material->name;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->material->name, 'url' => ['view', 'id' => $model->material->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Material movement');

?>

<div class="relocation-form">

    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'material-movement-active-form',
        'layout' => 'horizontal',
    ]);
    ?>

    <?= $form->field($model, 'stockId')->dropDownList($model->material->stockAliases, [
            'value' => $model->stockId,
            'readonly' => count($model->material->stocks) === 1
        ]
    ) ?>

    <?= $form->field($model, 'qty')->textInput([
        'type' => 'number', 'min' => 1, 'step' => 'any'
    ]) ?>

    <?= $form->field($model, 'newStockId')->dropDownList($model->destinationsList) ?>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('person-carry'), [
            'class' => 'btn btn-primary',
        ]) ?>
    </div>

    <?php ActiveForm::end();?>

</div>

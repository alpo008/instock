<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model app\models\StockOperation */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="stock-operation-form">

    <?php $form = ActiveForm::begin([
        'id' => 'stock-operation-active-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-4',
                'offset' => 'offset-sm-4',
                'wrapper' => 'col-sm-8',
                'error' => '',
                'hint' => '',
            ],
        ],
    ]); ?>

    <?= $form->field($model, 'material_id')->textInput() ?>

    <?= $form->field($model, 'stock_id')->textInput() ?>

    <?= $form->field($model, 'operation_type', [
            'options' => ['style' => 'display:none;']
    ])->dropDownList($model::getOperationTypes()) ?>

    <?= $form->field($model, 'qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'from_to')->textInput([
            'maxlength' => true,
            'value' => $model->operation_type === $model::CORRECTION_OPERATION ?
                Yii::t('app', 'Correction') : ''
    ]) ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('save'), [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Save')
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

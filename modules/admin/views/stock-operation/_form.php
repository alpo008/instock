<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use rmrevin\yii\fontawesome\FAS;
use yii\jui\AutoComplete;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\StockOperation */
/* @var $form yii\bootstrap4\ActiveForm */
/* @var $fromToLabel string */

switch ($model->operation_type) {
    case $model::CREDIT_OPERATION:
        $fromToLabel = Yii::t('app', 'Material destination');
        $stocksList = $model->material->stockAliases;
        break;
    case $model::DEBIT_OPERATION:
        $fromToLabel = Yii::t('app', 'Material source');
        $stocksList = \app\models\Stock::getStocksList();
        break;
    case $model::CORRECTION_OPERATION:
        $fromToLabel = Yii::t('app', 'Correction reason');
        $stocksList = \app\models\Stock::getStocksList();
        break;
    default:
        $fromToLabel = Yii::t('app', 'Stock operation');
        $stocksList = \app\models\Stock::getStocksList();
    break;
}

?>

<div class="stock-operation-form">

    <?php $form = ActiveForm::begin([
        'id' => 'stock-operations-active-form',
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

    <?php if (false) : ?>
        <div class="form-group row">
            <label class="col-sm-4" for="material-autocomplete-widget">
                <?= Yii::t('app', 'Material') ?>
            </label>
            <div class="col-sm-8">
                <?= AutoComplete::widget(
                    [
                        'id' => 'material-autocomplete-widget',
                            'clientOptions' => [
                            'source' => $model->materialsAutocompleteData,
                            'minLength'=>'3',
                            'autoFill'=>true,
                            'select' => new JsExpression( '(event, ui) => {
                                let input = document.querySelector(\'[name="StockOperation[material_id]"]\');
                                if (!isNaN(ui.item.id) && input !== null) {
                                    input.value = ui.item.id;
                                    event.target.classList.remove("is-invalid");
                                    event.target.classList.add("is-valid");
                                } else {
                                    event.target.classList.remove("is-invalid");
                                    event.target.classList.add("is-invalid");
                                }
                             }'
                        )
                    ],
                    'options' => [
                        'placeholder' => Yii::t('app', 'Start to type ref or name')
                    ]
                ],

                )
                ?>
            </div>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'material_id', [
            'options' => ['style' => 'display:none;']
        ])->textInput() ?>

    <?= $form->field($model, 'stock_id')->dropdownList($stocksList , [
        'readonly' => count($stocksList) === 1
    ]) ?>

    <?= $form->field($model, 'qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'from_to')->textInput(['maxlength' => true])
        ->label($fromToLabel)
    ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('save'), [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Save')
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

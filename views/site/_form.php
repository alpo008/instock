<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $material \app\models\Material | null */
/* @var $stockOperation \app\models\StockOperation | null */

?>
<!-- Modal -->
<div class="modal fade"
     id="modalMaterialOperationForm"
     data-backdrop="static" data-keyboard="false"
     tabindex="-1"
     aria-labelledby="modalMaterialOperationFormLabel"
     aria-hidden="true"
>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalMaterialOperationFormLabel">
            <?= Yii::t('app', 'Take') . ' ' . $material->name ?>
        </h5>
        <?php
            if (!empty($material->photoPath)) {
              echo Html::img($material->photoPath, ['width' => '150', 'class' => 'img-material']);
            }
        ?>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <?php $form = ActiveForm::begin([
              'id' => 'stock-operations-modal-form',
              'action' => ['credit'],
              'enableAjaxValidation'=> true,
              'validateOnChange' => true,
              'enableClientValidation'=> false,
              'validationUrl' => ['validate-form'],
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

          <?= $form->field($stockOperation, 'material_id')->hiddenInput()->label(false) ?>

          <?= $form->field($stockOperation, 'stock_id')->dropdownList($material->stockAliases , [
              'readonly' => count($material->stockAliases) === 1,
              'onchange' => new \yii\web\JsExpression('
                $("#stockoperation-qty").val(null);
              ')
          ]) ?>

          <?= $form->field($stockOperation, 'qty')->textInput(['maxlength' => true]) ?>

          <?= $form->field($stockOperation, 'from_to')->textInput(['maxlength' => true])
              ->label(Yii::t('app', 'Material destination'))
          ?>

          <?= $form->field($stockOperation, 'comments')->textarea(['rows' => 3]) ?>


        <?php ActiveForm::end(); ?>
      </div>
      <div class="modal-footer">
          <div class="form-group action-buttons">
              <?= Html::submitButton(FAS::icon('save'), [
                  'class' => 'btn btn-success',
                  'title' => Yii::t('app', 'Save'),
                  'form' => 'stock-operations-modal-form'
              ]) ?>

              <?= Html::button(FAS::icon('power-off'), [
                  'class' => 'btn btn-danger',
                  'data-dismiss' => 'modal',
                  'title' => Yii::t('app', 'Close form')
              ]) ?>
          </div>
      </div>
    </div>
  </div>
</div>

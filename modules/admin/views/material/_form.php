<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model app\models\Material */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="material-form">

    <?php $form = ActiveForm::begin([
        'id' => 'material-active-form',
        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'ref')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'min_qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'max_qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit')->dropDownList($model->unitsList) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'photo')->fileInput() ?>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('save'), [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Save')
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

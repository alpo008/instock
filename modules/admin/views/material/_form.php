<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model app\models\Material */
/* @var $form yii\widgets\ActiveForm */
/* @var $photoPath string */

$photoPath = !empty($model->photoPath) ? $model->photoPath : '@web/icons/solid/no-photo.svg';

?>

<div class="material-form">
    <div class="material-photo">
        <?= Html::img($photoPath, ['class' => 'file-upload']) ?>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'material-active-form',
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

    <?= $form->field($model, 'ref')->textInput() ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'min_qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'max_qty')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'unit')->dropDownList($model->unitsList) ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'group')->dropDownList($model->groupsList) ?>

    <?= $form->field($model, 'photo', [ 'options' => ['style' => 'display:none;']])->fileInput() ?>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('save'), [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Save')
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

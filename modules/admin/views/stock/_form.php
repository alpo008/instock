<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model app\models\Stock */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stock-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('save'), [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Save')
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

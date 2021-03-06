<?php

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model \app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
            'id' => 'user-active-form',
            'layout' => 'horizontal'
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'surname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['type' => 'email', 'maxlength' => true]) ?>

    <?= $form->field($model, 'newPassword')->textInput(['type' => 'password', 'maxlength' => true]) ?>

    <?= $form->field($model, 'role')->dropDownList([ 'USER' => 'USER', 'ADMIN' => 'ADMIN', ], ['prompt' => '']) ?>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('save'),
            ['class' => 'btn btn-primary', 'title' => Yii::t('app', 'Save')]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

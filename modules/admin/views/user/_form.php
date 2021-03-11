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
        'layout' => 'horizontal',
        'options' => ['autocomplete' => 'off']
    ]); ?>

    <?= $form->field($model, 'name')->textInput([
            'maxlength' => true,
            'placeholder' => Yii::t('app', 'Russian letters only')
    ]) ?>

    <?= $form->field($model, 'surname')->textInput([
            'maxlength' => true,
            'placeholder' => Yii::t('app', 'Russian letters only')
    ]) ?>

    <?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput([
            'maxlength' => true,
            'placeholder' => Yii::t('app', 'Latin letters and digits')
    ]) ?>

    <?= $form->field($model, 'email')->textInput(['type' => 'email', 'maxlength' => true]) ?>

    <?php if ($model->isNewRecord) : ?>

        <?= $form->field($model, 'newPassword')->textInput([
            'type' => 'password',
            'maxlength' => true,
            'required' => true,
            'placeholder' => Yii::t('app', 'Latin letters, digits, special symbols')
        ])->label($model->getAttributeLabel('password')) ?>

    <?php else : ?>

    <?= $form->field($model, 'newPassword')->textInput([
            'type' => 'password',
            'maxlength' => true,
            'placeholder' => Yii::t('app', 'Fill only if you want to change old one')
        ]) ?>

    <?php endif; ?>

    <?= $form->field($model, 'role')->dropDownList([ 'USER' => 'USER', 'ADMIN' => 'ADMIN', ], ['prompt' => '']) ?>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('save'),
            ['class' => 'btn btn-primary', 'title' => Yii::t('app', 'Save')]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

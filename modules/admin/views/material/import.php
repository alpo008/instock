<?php

use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\MaterialImport */

$this->title = Yii::t('app', 'Import');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Materials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/*$this->registerJs(
    file_get_contents(Yii::getAlias('@admin/views/material/') . 'import.js'),
    $this::POS_END
);*/

?>
<div class="material-import">

    <div class="action-buttons">
        <h1><?= Yii::t('app', 'Import materials from file') ?></h1>
    </div>

    <?php $form = ActiveForm::begin([
        'id' => 'material-import-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-md-4',
                'offset' => 'offset-sm-4',
                'wrapper' => 'col-md-8',
            ]
        ]
    ]); ?>

    <hr>

    <?= $form->field($model, 'skipFirstRow', )->checkbox() ?>

    <hr>

    <?= $form->field($model, 'duplicatedKeyAction')
        ->dropdownList($model->duplicatedKeyActionsList)
        ->hint('( ' . Yii::t('app', 'Applying to column \'ref\'') . ' )') ?>

    <hr>

    <?= $form->field($model, 'file')->fileInput() ?>

    <hr>

    <div class="form-group action-buttons">
        <?= Html::submitButton(FAS::icon('file-import'), [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Import'),
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

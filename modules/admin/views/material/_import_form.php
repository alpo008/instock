<?php

use rmrevin\yii\fontawesome\FAS;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\MaterialImport */

$this->registerJs(
    file_get_contents(Yii::getAlias('@admin/views/material/') . 'import.js'),
    $this::POS_END
);

?>


<!-- Modal -->
<div class="modal fade" id="materialImportModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="materialImportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="action-buttons">
                    <h1><?= Yii::t('app', 'Import materials from file') ?></h1>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="material-import">
                    <?php $form = ActiveForm::begin([
                        'id' => 'material-import-form',
                        'layout' => 'horizontal',
                        'action' => ['import'],
                        'options' => [
                            'class' => 'modal-form',
                        ],
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

                    <?= $form->field($model, 'file')->fileInput() ?>

                    <hr>

                    <div class="form-group action-buttons">
                        <?= Html::submitButton(FAS::icon('file-import'), [
                            'class' => 'btn btn-success',
                            'title' => Yii::t('app', 'Import'),
                        ]) ?>
                    </div>

                    <?php ActiveForm::end(); ?>

                    <div class="progress" style="display: none;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                             role="progressbar"
                             style="width: 0;"
                             aria-valuenow="10"
                             aria-valuemin="0"
                             aria-valuemax="100"
                        >
                            0%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

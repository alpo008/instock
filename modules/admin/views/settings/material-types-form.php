<?php

/* @var $this yii\web\View */
/* @var $materialTypes array */

use yii\bootstrap4\Html;
use rmrevin\yii\fontawesome\FAS;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Materials types');
$this->params['breadcrumbs'][] = Yii::t('app', 'Settings') . ' > ' . $this->title;

Pjax::begin(['id' => 'settings-types-pjax-container'])
?>

<div class="action-buttons">
    <h1><?= Yii::t('app', 'Materials types list edition') ?></h1>
</div>
<?= Html::beginForm(['settings/material-types'], 'post', [
    'id' => 'settings-types-form',
    'class' => 'settings-form',
    'data-pjax' => '1'
]) ?>
    <table class="table table-borderless">
        <?php if (!empty($materialTypes) && is_array($materialTypes)) : ?>
            <?php foreach ($materialTypes as $materialType) : ?>
                <tr>
                    <td>
                        <div class="form-group">
                            <?= Html::textInput("materialTypes[$materialType]", $materialType, [
                                'class' => 'form-control',
                                'maxlength' => 32
                            ]) ?>
                        </div>
                    </td>
                    <td>
                        <?= Html::a(
                                FAS::icon('trash-alt'),
                                ['settings/material-types'],
                                [
                                    'data' => [
                                        'pjax' => '1',
                                        'method' => 'post',
                                        'params' => [
                                            'action' => 'delete',
                                            'group' => $materialType
                                        ]
                                    ],
                                    'title' => Yii::t('app', 'Delete type')
                                ]
                            );
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <tr>
            <td>
                <div class="form-group">
                    <?= Html::textInput("materialTypes[new]", '', [
                        'class' => 'form-control',
                        'placeholder' => Yii::t('app', 'Enter new type name'),
                        'maxlength' => 32
                    ]) ?>
                </div>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                <div class="form-group action-buttons">
                    <?= Html::submitButton(FAS::icon('save'), [
                        'class' => 'btn btn-success',
                        'style' => 'margin-right:-40px',
                        'title' => Yii::t('app', 'Save')
                    ]) ?>
                </div>
            </td>
            <td></td>
        </tr>
    </table>
<?php echo Html::endForm();
Pjax::end();
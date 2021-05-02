<?php

/* @var $this yii\web\View */
/* @var $materialGroups array */

use yii\bootstrap4\Html;
use rmrevin\yii\fontawesome\FAS;
use yii\widgets\Pjax;

$this->title = Yii::t('app', 'Groups');
$this->params['breadcrumbs'][] = Yii::t('app', 'Settings') . ' > ' . $this->title;

Pjax::begin(['id' => 'settings-groups-pjax-container'])
?>
<?= Html::beginForm(['settings/material-groups'], 'post', [
    'id' => 'settings-groups-form',
    'class' => 'settings-form',
    'data-pjax' => '1'
]) ?>
    <table class="table table-borderless">
        <?php if (!empty($materialGroups) && is_array($materialGroups)) : ?>
            <?php foreach ($materialGroups as $materialGroup) : ?>
                <tr>
                    <td>
                        <div class="form-group">
                            <?= Html::textInput("materialGroups[$materialGroup]", $materialGroup, [
                                'class' => 'form-control',
                                'maxlength' => 16
                            ]) ?>
                        </div>
                    </td>
                    <td>
                        <?= Html::a(
                                FAS::icon('trash-alt'),
                                ['settings/material-groups'],
                                [
                                    'data' => [
                                        'pjax' => '1',
                                        'method' => 'post',
                                        'params' => [
                                            'action' => 'delete',
                                            'group' => $materialGroup
                                        ]
                                    ],
                                    'title' => Yii::t('app', 'Delete group')
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
                    <?= Html::textInput("materialGroups[new]", '', [
                        'class' => 'form-control',
                        'placeholder' => Yii::t('app', 'Enter new machine name'),
                        'maxlength' => 16
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
<?php

/** @var $value string | mixed */
/** @var $url string */
/** @var $formId string */
/** @var $containerId string */
/** @var $successCallback string */
/** @var $errorCallback string */
/** @var $input string */

use yii\helpers\Html;
use rmrevin\yii\fontawesome\FAS;

echo Html::tag('span', $value, ['class' => 'cell-editable-value']);
echo Html::beginForm($url, 'POST', ['class' => 'in-grid-form']);
echo $input . ' ';
echo Html::endForm();
echo Html::a(FAS::icon('edit'), '#', [
    'class' => 'cell-editable__icon_edit',
    'title' => Yii::t('app', 'Edit')
]);
echo Html::a(FAS::icon('times'), '#', [
    'class' => 'cell-editable__icon_cancel',
    'title' => Yii::t('app', 'Cancel')
]);
echo Html::a(FAS::icon('save'), '#', [
    'class' => 'cell-editable__icon_save',
    'title' => Yii::t('app', 'Save')
]);
echo Html::beginTag('div', ['class' => 'cell-editable__error-messages']);
echo Html::endTag('div');


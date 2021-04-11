<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $urgent integer | null */

$this->title = Yii::t('app', 'Admin page');

?>
<div class="admin-default-index">
    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>
    <?php if(!empty($urgent)) : ?>
        <div class="alert alert-danger" role="alert">
            <?= Html::a(Yii::t('app', 'View urgent messages') . ' (' . $urgent . ')', [
                '/admin/material',
                'MaterialSearch[quantity]' => \app\modules\admin\models\MaterialSearch::LESS_THAN_MIN_QTY,
                'sort' => '-quantity',
            ], ['class' => 'link-urgent']) ?>
        </div>
    <?php else: ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::t('app', 'There are no urgent messages') ?>
        </div>
    <?php endif; ?>
</div>

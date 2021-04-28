<?php

use yii\jui\Sortable;

/* @var $this yii\web\View */
/* @var $model \app\modules\admin\models\MaterialExport */
/* @var $columns array */

$this->title = Yii::t('app', 'Edit material export format');
//$this->params['breadcrumbs'][] = Yii::t('app', 'Settings');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php if (!empty($columns) && is_array($columns)) : ?>

    <table class="table table-responsive">
    <?php $label = 'A'; ?>
    <tr>
        <?php for ($i = 0; $i < count($columns); $i++) : ?>
            <th>
                <?= $label++?>
            </th>
        <?php endfor; ?>
    </tr>
    <tr>
            <?= Sortable::widget([
            'items' => array_keys($columns),
            'options' => ['tag' => 'tr'],
            'itemOptions' => ['tag' => 'td'],
            'clientOptions' => ['cursor' => 'move'],
            ]); ?>
    </tr>
    </table>

<?php endif; ?>

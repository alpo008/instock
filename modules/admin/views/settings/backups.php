<?php

use yii\bootstrap4\Html;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $files array */

$this->title = Yii::t('app', 'Backups');
$this->params['breadcrumbs'][] = Yii::t('app', 'Settings') . ' > ' . $this->title;

?>

<div class="action-buttons">
    <h1><?= $this->title ?></h1>
</div>

<?php if (!empty($files) && is_array($files)) : ?>
    <table class="table table-bordered settings-form" style="width: 50%;">
    <?php foreach ($files as $date => $file): ?>
        <tr>
            <th><?= Yii::t('app', 'Created at') ?></th>
            <th></th>
        </tr>
        <tr>
            <td>
                <?= $date ?>
            </td>
            <td style="text-align: center;">
                <?= Html::a(FAS::icon('download'), ['backup'], [
                        'title' => Yii::t('app', 'Download'),
                        'data' => [
                        'method' => 'post',
                        'params' => [
                            'file' => $file
                        ]
                    ]
                ]) ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
<?php endif;

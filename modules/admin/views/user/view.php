<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->fullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->fullName;
\yii\web\YiiAsset::register($this);

?>
<div class="user-view">

    <div class="action-buttons">
        <h1><?= $model->fullName . ' ( ' . $model->position . ' ) '; ?></h1>
        <?= Html::a(FAS::icon('user-edit'),
            ['update', 'id' => $model->id],
            ['class' => 'btn btn-primary', 'title' => Yii::t('app', 'Update')]) ?>
        <?php if ($model->status === $model::STATUS_ACTIVE) : ?>
        <?= Html::a(FAS::icon('user-times'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'title' => Yii::t('app', 'Block'),
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to block this user') . ' ?',
                'method' => 'post',
            ],
        ]) ?>
        <?php elseif ($model->status === $model::STATUS_DISABLED) : ?>
            <?= Html::a(FAS::icon('user-check'), ['restore', 'id' => $model->id], [
                'class' => 'btn btn-success',
                'title' => Yii::t('app', 'Restore'),
                'data' => [
                    'confirm' => Yii::t('app', 'User with role {role} will be restored', [
                        'role' => $model->role
                    ]) . '!',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </div>

    <?= DetailView::widget([
        'id' => 'user-detail-view',
        'model' => $model,
        'options' => ['class' => 'table detail-view'],
        'attributes' => [
            'username',
            'email:email',
            'role',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => $model->statusIcon . ' ' . $model->statusText
            ],
            [
                'attribute' => 'created_at',
                'value' => Yii::$app->formatter->asDate($model->created_at) .
                    ' ' . Yii::$app->formatter->asTime($model->created_at)
            ],
            [
                'attribute' => 'updated_at',
                'value' => Yii::$app->formatter->asDate($model->updated_at) .
                    ' ' . Yii::$app->formatter->asTime($model->updated_at)
            ],
        ],
    ]) ?>

</div>

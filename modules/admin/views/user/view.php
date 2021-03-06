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
        <?= Html::a(FAS::icon('user-times'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'title' => Yii::t('app', 'Delete'),
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>

    <?= DetailView::widget([
        'model' => $model,
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

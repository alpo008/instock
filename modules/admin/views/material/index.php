<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\MaterialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Materials');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="material-index">

    <div class="action-buttons">
    <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a(FAS::icon('layer-plus'), ['create'], [
            'class' => 'btn btn-success',
            'title' => Yii::t('app', 'Add material')
        ]) ?>
    </div>
    <?php Pjax::begin(['id' => 'material-index-pjax-container']); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'id' => 'material-index-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ref',
            'name',
            'qty',
            'min_qty',
            //'max_qty',
            //'unit',
            //'type',
            //'group',
            //'created_at',
            //'updated_at',
            //'created_by',
            //'updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

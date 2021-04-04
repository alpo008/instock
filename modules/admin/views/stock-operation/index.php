<?php


use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use rmrevin\yii\fontawesome\FAS;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\admin\models\StockOperationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stock operations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stock-operation-index">

    <div class="action-buttons">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= Html::a('<span class="before-icon">+</span>' . FAS::icon('file-invoice'), ['create-debit'], [
                'class' => 'btn btn-success',
                'style' => 'position:relative;',
                'title' => Yii::t('app', 'Create debit operation')
        ]) ?>

        <?= Html::a('<span class="before-icon">-</span>' . FAS::icon('file-invoice'), ['create-credit'], [
                'class' => 'btn btn-success',
                'style' => 'position:relative;',
                'title' => Yii::t('app', 'Create credit operation')
        ]) ?>

        <?= Html::a(FAS::icon('ballot-check'), ['create-correction'], [
                'class' => 'btn btn-danger',
                'style' => 'position:relative;',
                'title' => Yii::t('app', 'Create correction operation')
        ]) ?>

    </div>

    <?php Pjax::begin(['id' => 'stock-operations-index-pjax-container']); ?>

    <?= GridView::widget([
        'id' => 'stock-operations-index-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'material_id',
            'stock_id',
            'operation_type',
            'qty',
            //'from_to',
            //'comments:ntext',
            //'created_at',
            //'created_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

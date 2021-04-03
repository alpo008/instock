<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "stock_operations".
 *
 * @property int $id ИД
 * @property int $material_id ИД материала
 * @property int $stock_id ИД складского места
 * @property int $operation_type Тип операции
 * @property float|null $qty Количество материала
 * @property string $from_to Источник или получатель
 * @property string|null $comments Комментарий
 * @property string|null $created_at Дата добавления
 * @property int|null $created_by Кто создал
 *
 * @property Material $material
 * @property Stock $stock
 * @property string $materialName
 * @property string $materialRef
 * @property string $stockAlias
 */
class StockOperation extends \yii\db\ActiveRecord
{
    const CREDIT_OPERATION = 1;
    const DEBIT_OPERATION = 2;
    const CORRECTION_OPERATION = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stock_operations}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_id', 'stock_id', 'operation_type', 'from_to', 'qty'], 'required'],
            [['material_id', 'stock_id', 'operation_type'], 'integer'],
            [['qty'], 'number'],
            [['comments'], 'string'],
            [['created_at', 'created_by'], 'safe'],
            [['from_to'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'material_id' => Yii::t('app', 'Material'),
            'stock_id' => Yii::t('app', 'Stock place'),
            'operation_type' => Yii::t('app', 'Operation type'),
            'qty' => Yii::t('app', 'Quantity'),
            'from_to' => Yii::t('app', 'Source or destination'),
            'comments' => Yii::t('app', 'Comments'),
            'created_at' => Yii::t('app', 'Created at'),
            'created_by' => Yii::t('app', 'Created by'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => Yii::$app->formatter->asDatetime(time())
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => false,
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial ()
    {
        return $this->hasOne(Material::class, ['id' => 'material_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStock ()
    {
        return $this->hasOne(Stock::class, ['id' => 'stock_id']);
    }

    /**
     * @return string
     */
    public function getMaterialName ()
    {
        return $this->material instanceof Material ? $this->material->name : '';
    }

    /**
     * @return string
     */
    public function getMaterialRef ()
    {
        return $this->material instanceof Material ? $this->material->ref : '';
    }

    /**
     * @return string
     */
    public function getStockAlias ()
    {
        return $this->stock instanceof Stock ? $this->stock->alias : '';
    }

    /**
     * @return array
     */
    public static function getOperationTypes ()
    {
        return [
            self::CREDIT_OPERATION => Yii::t('app', 'Credit'),
            self::DEBIT_OPERATION => Yii::t('app', 'Debit'),
            self::CORRECTION_OPERATION => Yii::t('app', 'Correction'),
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "materials_stocks".
 *
 * @property int $id ИД
 * @property int $material_id ИД материала
 * @property int $stock_id ИД складского места
 * @property float|null $qty Количество
 *
 * @property Material $material
 * @property Stock $stock
 */
class MaterialStock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'materials_stocks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_id', 'stock_id'], 'required'],
            [['material_id', 'stock_id'], 'integer'],
            [['qty'], 'number'],
            [['material_id', 'stock_id'], 'unique', 'targetAttribute' => ['material_id', 'stock_id']],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => Material::class, 'targetAttribute' => ['material_id' => 'id']],
            [['stock_id'], 'exist', 'skipOnError' => true, 'targetClass' => Stock::class, 'targetAttribute' => ['stock_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'material_id' => Yii::t('app', 'Material ID'),
            'stock_id' => Yii::t('app', 'Stock ID'),
            'qty' => Yii::t('app', 'Qty'),
        ];
    }

    /**
     * Gets query for [[Material]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Material::class, ['id' => 'material_id']);
    }

    /**
     * Gets query for [[Stock]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStock()
    {
        return $this->hasOne(Stock::class, ['id' => 'stock_id']);
    }
}

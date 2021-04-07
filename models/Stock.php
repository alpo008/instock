<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "stocks".
 *
 * @property int $id ИД
 * @property string $alias Обозначение
 * @property string|null $description Описание
 *
 * @property MaterialStock[] $materialsStocks
 * @property Material[] $materials
 */
class Stock extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%stocks}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['alias'], 'required'],
            [['description'], 'string'],
            [['alias'], 'string', 'max' => 32],
            [['alias'], 'unique']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'alias' => Yii::t('app', 'Alias'),
            'description' => Yii::t('app', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialsStocks()
    {
        return $this->hasMany(MaterialStock::class, ['stock_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Material::class, ['id' => 'material_id'])
            ->via('materialsStocks');
    }

    /**
     * List of all stocks ['id' => 'alias']
     * @return array
     */
    public static function getStocksList ()
    {
        $stocksList = [];
        if ($allStocks = self::find()->select(['id', 'alias'])->asArray()->all()) {
            $stocksList = array_column($allStocks, 'alias', 'id');
        }
        return $stocksList;
    }
}

<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "materials".
 *
 * @property int $id ИД
 * @property int $ref Номер САП
 * @property string $name Наименование
 * @property float|null $qty Текущее кол-во
 * @property float|null $min_qty Крит. низкое кол-во
 * @property float|null $max_qty Макс. кол-во
 * @property int $unit Ед. измерения
 * @property string|null $type Тип
 * @property string|null $group Группа
 * @property string|null $created_at Дата добавления
 * @property string|null $updated_at Дата изменения
 * @property int|null $created_by Кто добавил
 * @property int|null $updated_by Кто отредактировал
 *
 * @property array $unitsList
 * @property string $unitName
 */
class Materials extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%materials}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ref', 'name'], 'required'],
            [['ref', 'unit', 'created_by', 'updated_by'], 'integer'],
            [['qty', 'min_qty', 'max_qty'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['type', 'group'], 'string', 'max' => 16],
            [['ref'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ref' => Yii::t('app', 'Ref'),
            'name' => Yii::t('app', 'Material name'),
            'qty' => Yii::t('app', 'Qty'),
            'min_qty' => Yii::t('app', 'Min qty'),
            'max_qty' => Yii::t('app', 'Max qty'),
            'unit' => Yii::t('app', 'Unit'),
            'type' => Yii::t('app', 'Type'),
            'group' => Yii::t('app', 'Group'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'created_by' => Yii::t('app', 'Created by'),
            'updated_by' => Yii::t('app', 'Updated by'),
        ];
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * List of units
     * @return array
     */
    public function getUnitsList ()
    {
        return [
            0 => Yii::t('app', 'Not set'),
            1 => Yii::t('app', 'Pcs'),
            2 => Yii::t('app', 'Meters'),
            3 => Yii::t('app', 'Pairs'),
            4 => Yii::t('app', 'Kg'),
        ];
    }

    /**
     * Measurement unit name
     * @return string
     */
    public function getUnitName ()
    {
        return !empty($this->unitsList[$this->unit]) ?
            $this->unitsList[$this->unit] :
            $this->unitsList[0];
    }
}

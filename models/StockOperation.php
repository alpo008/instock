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
 * @property User $creator
 * @property string $materialName
 * @property string $materialRef
 * @property string $stockAlias
 * @property array $materialsAutocompleteData
 * @property array $materialAvailability
 * @property string $creatorName
 * @property string $operationTime
 * @property string $operationType
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
            [['qty'], 'qtyValidator']
        ];
    }

    /**
     * Quantity validator
     */
    public function qtyValidator()
    {
        if ($this->material instanceof Material &&
            !empty($this->qty) && !empty($this->stock_id) &&
            (int)$this->operation_type === self::CREDIT_OPERATION)
        {
            $max = $this->material->getQuantity($this->stock_id);
            if ($this->qty > $max) {
                $this->addError('qty',
                    Yii::t('app', 'The rest is only {max} at stock place', compact('max')) .
                    ' ' . $this->material->unitName
                );
            }
        }
        if ((int)$this->operation_type === self::CORRECTION_OPERATION && $this->qty < 0) {
            $this->addError('qty', Yii::t('app', 'Quantity can not be less than') . ' 0');
        }
        if ((int)$this->operation_type !== self::CORRECTION_OPERATION && $this->qty <= 0) {
            $this->addError('qty', Yii::t('app', 'Quantity can not be less than') . ' 0');
        }

        $fractional = [Material::UNIT_KG, Material::UNIT_METERS];

        if (is_numeric($this->qty) && floor($this->qty) != $this->qty && !in_array($this->material->unit, $fractional)) {
            $this->addError('qty', Yii::t('app', 'For this material quantity can be only integer'));
        }
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
            'materialName' => Yii::t('app', 'Material'),
            'materialRef' => Yii::t('app', 'Ref'),
            'creatorName' => Yii::t('app', 'Responsible person'),
            'operationType' => Yii::t('app', 'Operation type'),
            'stockAlias' => Yii::t('app', 'Stock place'),
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
     * @inheritDoc
     */
    public function beforeSave($insert)
    {
        $params = ['material_id' => $this->material_id,'stock_id' => $this->stock_id];
        if (!$materialStock = MaterialStock::findOne($params)) {
            $materialStock = new MaterialStock($params);
        }
        switch ($this->operation_type) {
            case self::CREDIT_OPERATION :
                $materialStock->qty -= $this->qty;
            break;
            case self::DEBIT_OPERATION :
                $materialStock->qty += $this->qty;
            break;
            case self::CORRECTION_OPERATION :
                $materialStock->qty = $this->qty;
            break;
        }
        if ($materialStock->qty < 0) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Can not accept negative quantity'));
        }
        $transaction = Yii::$app->db->beginTransaction();
        $result = parent::beforeSave($insert);
        try {
            if (!$materialStock->isNewRecord && $materialStock->qty == 0) {
                $result &= !!$materialStock->delete();
            } elseif ($materialStock->qty > 0) {
                $result &= $materialStock->save();
            }
            $transaction->commit();
        } catch (\Exception | \Throwable $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Quantity update error'));
            return false;
        }
        return $result;
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
     * Operation author
     * @return \yii\db\ActiveQuery
     */
    public function getCreator ()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Operation author name
     * @return string
     */
    public function getCreatorName ()
    {
        return $this->creator instanceof User ? $this->creator->fullName : '?';
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
     * Operation date and time
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getOperationTime () {
        return Yii::$app->formatter->asDate($this->created_at) . ' ' .
            Yii::$app->formatter->asTime($this->created_at);
    }

    /**
     * Operation type
     * @return string
     */
    public function getOperationType ()
    {
        return self::getOperationTypes()[$this->operation_type] ?
            self::getOperationTypes()[$this->operation_type] : '?';
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

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMaterialsAutocompleteData ()
    {
        return Material::find()
            ->select(['CONCAT(ref, " ; ", name) as value', 'CONCAT(ref, " ; ", name) as  label','id as id'])
            ->asArray()
            ->all();
    }

    /**
     * @return array
     */
    public function getMaterialAvailability ()
    {
        $availability = [];
        if (!empty($this->material) && !empty($this->material->stocks)) {
            foreach ($this->material->stocks as $stock) {
                $availability[$stock->id] = $this->material->getQuantity($stock->id);
            }
        }
        return $availability;
    }
}

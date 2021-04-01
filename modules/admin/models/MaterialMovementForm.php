<?php


namespace app\modules\admin\models;

use Yii;
use app\models\Material;
use app\models\MaterialStock;
use app\models\Stock;
use yii\db\StaleObjectException;

/**
 * Class MaterialMovementForm
 * @package app\modules\admin\models
 *
 * @property integer $materialId;
 * @property integer $stockId;
 * @property integer $newStockId;
 * @property number $qty;
 *
 * @property Material $material;
 * @property array $destinationsList;
 */
class MaterialMovementForm extends \yii\base\Model
{
    public $materialId;
    public $stockId;
    public $newStockId;
    public $qty;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['materialId', 'stockId', 'newStockId', 'qty'], 'required'],
            [['materialId', 'stockId', 'newStockId', 'qty'], 'number'],
            [['stockId', 'newStockId'], 'validateStocksId'],
            ['qty', 'validateQty']
        ];
    }

    /**
     * Stock id validator
     */
    public function validateStocksId()
    {
        if ($this->newStockId === $this->stockId){
            $this->addError('newStockId',
                Yii::t('app', 'Source and target places can not be the same') . ' !');
        }
    }

    /**
     * Quantity validator
     */
    public function validateQty()
    {
        if ((int) $this->qty <= 0) {
            $this->addError('qty', 'Moving quantity can not be zero');
        }
        $existingQty = MaterialStock::find()
            ->select('qty')
            ->where(['material_id' => $this->materialId])
            ->andWhere(['stock_id' => $this->stockId])
            ->scalar();

        if (!$existingQty || $existingQty < $this->qty){
            $this->addError('qty',
                Yii::t('app', 'Quantity can not be greater then exists on place') .
                ' ( ' . Yii::t('app', 'rest') . ': ' .
                $existingQty . ' ' . $this->material->unit .' ) !'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'materialId' => Yii::t('app', 'Material'),
            'stockId' => Yii::t('app', 'From where'),
            'newStockId' => Yii::t('app', 'To where'),
            'qty' => Yii::t('app', 'How many'),
        ];
    }

    public function save ()
    {
        if (!$this->validate()) {
            return false;
        }
        if (!$currentLocation = MaterialStock::findOne([
            'material_id' => $this->materialId,
            'stock_id' => $this->stockId
        ]))
        {
            return false;
        }
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $currentLocation->qty -= $this->qty;
            $newLocation = MaterialStock::findOne([
                'material_id' => $this->materialId,
                'stock_id' => $this->newStockId
            ]);
            if ($newLocation instanceof MaterialStock) {
                $newLocation->qty += $this->qty;
            } else {
                $newLocation = new MaterialStock([
                    'material_id' => $this->materialId,
                    'stock_id' => $this->newStockId,
                    'qty' => $this->qty
                ]);
            }
            if (!$currentLocation->qty) {
                try {
                    $currentLocation->delete();
                } catch (StaleObjectException $e) {
                    $transaction->rollBack();
                    return false;
                } catch (\Throwable $e) {
                    $transaction->rollBack();
                    return false;
                }
            } else {
                if (!$currentLocation->save()) {
                    $transaction->rollBack();
                    return false;
                }
            }
            if (!$newLocation->save()) {
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            return false;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            return false;
        }
        return true;
    }

    /**
     * @return Material|null
     */
    protected function getMaterial()
    {
        return Material::findOne($this->materialId);
    }

    /**
     * @return array
     */
    public function getDestinationsList ()
    {
        $destinations = Stock::find()
            ->select('DISTINCT(stocks.alias), id')
            ->where(['<>', 'stocks.id', $this->stockId])
            ->orderBy('stocks.alias')
            ->asArray()
            ->all();

        if (!empty($destinations && is_array($destinations))) {
            return array_column($destinations, 'alias', 'id');
        }
        return [];
    }
}
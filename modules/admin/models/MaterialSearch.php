<?php

namespace app\modules\admin\models;

use app\models\Stock;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Material;

/**
 * MaterialSearch represents the model behind the search form of `app\models\Material`.
 *
 * @property float $quantity
 * @property array $quantityFilter
 * @property string $stockAliases
 * @property array $stockAliasesFilter
 */
class MaterialSearch extends Material
{

    const QTY_IN_RANGE = '1';
    const LESS_THAN_MIN_QTY = '2';
    const GREATER_THAN_MAX_QTY = '3';

    public $quantity;
    public $stockAliases;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ref', 'unit', 'created_by', 'updated_by'], 'integer'],
            [['name', 'type', 'group', 'created_at', 'updated_at', 'quantity'], 'safe'],
            [['min_qty', 'max_qty'], 'number'],
            [['stockAliases'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Material::find()->joinWith(['materialsStocks'])->groupBy(['materials.ref'])
        ->joinWith('stocks');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'ref',
                'name',
                'min_qty',
                'max_qty',
                'unit',
                'quantity' => [
                    'asc' => ['SUM({{%materials_stocks}}.qty)' => SORT_ASC],
                    'desc' => ['SUM({{%materials_stocks}}.qty)' => SORT_DESC],
                ],
                'stockAliases' => [
                    'asc' => ['SUM({{%stocks}}.alias)' => SORT_DESC],
                    'desc' => ['SUM({{%stocks}}.alias)' => SORT_ASC],
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            //'id' => $this->id,
            //'ref' => $this->ref,
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
            'unit' => $this->unit,
            //'created_at' => $this->created_at,
            //'updated_at' => $this->updated_at,
            //'created_by' => $this->created_by,
            //'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'ref', $this->ref])
            ->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'group', $this->group]);

        $query->andFilterWhere(['like', '{{%stocks}}.alias', $this->stockAliases]);

        switch ($this->quantity) {
            case self::LESS_THAN_MIN_QTY:
                $query->having('COALESCE(SUM({{%materials_stocks}}.qty), 0) <= COALESCE({{%materials}}.min_qty, 0)');
            break;
            case self::GREATER_THAN_MAX_QTY:
                $query->having('SUM({{%materials_stocks}}.qty) > {{%materials}}.max_qty');
            break;
            case self::QTY_IN_RANGE:
                $query->having('COALESCE(SUM({{%materials_stocks}}.qty), 0) > COALESCE({{%materials}}.min_qty, 0) 
                    AND COALESCE(SUM({{%materials_stocks}}.qty), 0) < COALESCE({{%materials}}.max_qty, 1)
                ');
            break;
        }

        return $dataProvider;
    }

    /**
     * @return array
     */
    public function getQuantityFilter()
    {
        return [
            self::QTY_IN_RANGE => \Yii::t('app', 'Quantity in range'),
            self::LESS_THAN_MIN_QTY => \Yii::t('app', 'Less than min quantity'),
            self::GREATER_THAN_MAX_QTY => \Yii::t('app', 'Greater than max quantity')
        ];
    }

    /**
     * @return array
     */
    public function getStockAliasesFilter ()
    {
        $aliasesFilter = [];
        $stocks = Stock::find()->all();
        if (!empty($stocks) && is_array($stocks)) {
            $aliasesFilter = array_column($stocks, 'alias', 'alias');
        }
        return $aliasesFilter;
    }
}

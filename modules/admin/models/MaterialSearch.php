<?php

namespace app\modules\admin\models;

use app\models\Stock;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Material;
use yii\db\QueryInterface;

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
                'type',
                'group',
                'min_qty',
                'max_qty',
                'unit',
                'quantity' => [
                    'asc' => ['SUM({{%materials_stocks}}.qty)' => SORT_ASC],
                    'desc' => ['SUM({{%materials_stocks}}.qty)' => SORT_DESC],
                ],
                'stockAliases' => [
                    'asc' => ['COUNT({{%stocks}}.alias)' => SORT_ASC],
                    'desc' => ['COUNT({{%stocks}}.alias)' => SORT_DESC],
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

        $re1 = '/(\d+)(\,{1})(\d+)/';
        $re2 = '/(\d+)(\.{1})(\d+)/';
        $repl1 = '$1.$3';
        $repl2 = '$1,$3';
        $name = '';
        $name1 = '';
        $name2 = '';

        if (!empty($this->name) && is_string($this->name)) {
            $name1 = preg_replace($re1, $repl1, $this->name);
            $name2 = preg_replace($re2, $repl2, $this->name);
            $name = str_replace(' ', '', $this->name);
            $name1 = str_replace(' ', '', $name1);
            $name2 = str_replace(' ', '', $name2);
        }

        $query->andFilterWhere(['OR', 
                ['like', "REPLACE(name, ' ', '')", $name],
                ['like', "REPLACE(name, ' ', '')", $name1],
                ['like', "REPLACE(name, ' ', '')", $name2]
            ]
        )
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

    /**
     * @param QueryInterface $query
     * @param string $sort
     * @return array|Material[]
     */
    public function getSortedModels(QueryInterface $query, string $sort)
    {
        if (is_string($sort)) {
            if (strpos($sort, '-') !== false) {
                $attribute = trim($sort, ' -');
                $direction = SORT_DESC;
            } else {
                $attribute = trim($sort);
                $direction = SORT_ASC;
            }
            if ($attribute === 'quantity') {
                return $query->orderBy(['SUM({{%materials_stocks}}.qty)' => $direction])->all();
            }
            if ($attribute === 'stockAliases') {
                return $query->orderBy(['COUNT({{%stocks}}.alias)' => $direction])->all();
            }
            return $query->orderBy([$attribute => $direction])-> all();
        }
        return [];
    }
}

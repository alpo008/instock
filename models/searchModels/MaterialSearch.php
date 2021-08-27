<?php

namespace app\models\searchModels;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Material;
use app\models\Stock;

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
        $query = Material::find()
            ->joinWith(['materialsStocks'])
            ->groupBy(['materials.ref'])
            ->joinWith('stocks')
            ->having('SUM({{%materials_stocks}}.qty) > 0');

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
                'type',
                'group',
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
            'unit' => $this->unit,
            'min_qty' => $this->min_qty,
            'max_qty' => $this->max_qty,
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
        if (!empty($this->quantity)) {
            $query->andHaving(['=', 'SUM({{%materials_stocks}}.qty)', $this->quantity]);
        }

        return $dataProvider;
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

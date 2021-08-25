<?php

namespace app\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StockOperation;
use yii\db\QueryInterface;

/**
 * StockOperationSearch represents the model behind the search form of `app\models\StockOperation`.
 *
 * @property string $materialName
 * @property string $materialRef
 * @property string $stockAlias
 * @property string $operationType
 *
 */
class StockOperationSearch extends StockOperation
{
    public $materialName;
    public $materialRef;
    public $stockAlias;
    public $operationType;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'material_id', 'stock_id', 'operation_type', 'created_by'], 'integer'],
            [['qty'], 'number'],
            [['from_to', 'created_at', 'materialName', 'materialRef', 'stockAlias', 'operationType'], 'safe']
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
        $query = StockOperation::find()
            ->joinWith(['material', 'stock']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
/*            'pagination' => [
                'pageSize' => 10,
            ]*/
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'created_at',
                'qty',
                'operationType' => [
                    'asc' => ['operation_type' => SORT_ASC],
                    'desc' => ['operation_type' => SORT_DESC],
                ],
                'materialRef' => [
                    'asc' => ['{{%materials}}.ref' => SORT_ASC],
                    'desc' => ['{{%materials}}.ref' => SORT_DESC],
                ],
                'materialName' => [
                    'asc' => ['{{%materials}}.name' => SORT_ASC],
                    'desc' => ['{{%materials}}.name' => SORT_DESC],
                ],
                'stockAlias' => [
                    'asc' => ['{{%stocks}}.alias' => SORT_ASC],
                    'desc' => ['{{%stocks}}.alias' => SORT_DESC],
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
            'material_id' => $this->material_id,
            'stock_id' => $this->stock_id,
            'operation_type' => $this->operationType,
            'qty' => $this->qty,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', '{{%stock_operations}}.created_at', $this->created_at]);
        $query->andFilterWhere(['like', '{{%materials}}.name', $this->materialName]);
        $query->andFilterWhere(['like', '{{%materials}}.ref', $this->materialRef]);
        $query->andFilterWhere(['=', '{{%stocks}}.id', $this->stockAlias]);

        $query->andFilterWhere(['like', 'from_to', $this->from_to])
            ->andFilterWhere(['like', 'comments', $this->comments]);

        return $dataProvider;
    }

    /**
     * @param QueryInterface $query
     * @param string $sort
     * @return array|StockOperation[]
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
            if ($attribute === 'materialRef') {
                return $query->orderBy(['{{%materials}}.ref' => $direction])->all();
            }
            if ($attribute === 'materialName') {
                return $query->orderBy(['{{%materials}}.name' => $direction])->all();
            }
            if ($attribute === 'stockAliases') {
                return $query->orderBy(['{{%stocks}}.alias' => $direction])->all();
            }
            if ($attribute === 'operationType') {
                return $query->orderBy(['operation_type' => $direction])->all();
            }
            return $query->orderBy([$attribute => $direction])-> all();
        }
        return [];
    }
}

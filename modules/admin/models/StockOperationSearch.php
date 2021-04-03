<?php

namespace app\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StockOperation;

/**
 * StockOperationSearch represents the model behind the search form of `app\models\StockOperation`.
 *
 * @property string $materialName
 * @property string $materialRef
 * @property string $stockAlias
 *
 */
class StockOperationSearch extends StockOperation
{
    public $materialName;
    public $materialRef;
    public $stockAlias;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'material_id', 'stock_id', 'operation_type', 'created_by'], 'integer'],
            [['qty'], 'number'],
            [['from_to', 'created_at', 'materialName', 'materialRef', 'stockAlias'], 'safe']
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
            ->joinWith('material')
            ->joinWith('stock');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'material_id' => $this->material_id,
            'stock_id' => $this->stock_id,
            'operation_type' => $this->operation_type,
            'qty' => $this->qty,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]);

        $query->andFilterWhere(['like', 'from_to', $this->from_to])
            ->andFilterWhere(['like', 'comments', $this->comments]);

        return $dataProvider;
    }
}

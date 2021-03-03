<?php

namespace app\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form of `app\models\User`.
 *
 * @property string $fullName
 */
class UserSearch extends User
{
    /* Вычисляемое поле */
    public $fullName;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'status'], 'integer'],
            [['username', 'name', 'surname', 'position',
                'password_hash', 'auth_key', 'email', 'role',
                'created_at', 'updated_at', 'fullName'], 'safe'],
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
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'fullName' => [
                    'asc' => ['name' => SORT_ASC, 'surname' => SORT_ASC],
                    'desc' => ['name' => SORT_DESC, 'surname' => SORT_DESC],
                    'default' => SORT_ASC
                ],
                'username',
                'position',
                'email',
                'role',
                'updated_at'
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
            'id' => $this->id,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'position', $this->position])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'role', $this->role]);
        $query->andWhere('name LIKE "%' . $this->fullName . '%" ' .
            'OR surname LIKE "%' . $this->fullName . '%"'
        );

        return $dataProvider;
    }
}

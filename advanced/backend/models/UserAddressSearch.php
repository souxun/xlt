<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\UserAddress;

/**
 * UserAddressSearch represents the model behind the search form about `frontend\models\UserAddress`.
 */
class UserAddressSearch extends UserAddress
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['user_id', 'name', 'phone', 'fix_tel', 'zip_code', 'province', 'city', 'country', 'detail_address', 'create_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = UserAddress::find();

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
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'fix_tel', $this->fix_tel])
            ->andFilterWhere(['like', 'zip_code', $this->zip_code])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'country', $this->country])
            ->andFilterWhere(['like', 'detail_address', $this->detail_address]);

        return $dataProvider;
    }
}

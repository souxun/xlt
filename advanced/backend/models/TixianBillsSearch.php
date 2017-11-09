<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\TixianBills;

/**
 * TixianBillsSearch represents the model behind the search form about `frontend\models\TixianBills`.
 */
class TixianBillsSearch extends TixianBills
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status'], 'integer'],
            [['user_id', 'nickname', 'account', 'phone', 'create_time'], 'safe'],
            [['money'], 'number'],
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
        $query = TixianBills::find();

        $query->select(['id','user_id','nickname','money','type','account','status','phone','create_time'])->orderBy( [
            'create_time' => SORT_DESC,
        ]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'account' => $this->account,
            'user_id' => $this->user_id,
            'money' => $this->money,
            'type' => $this->type,
            'status' => $this->status,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'account', $this->account])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }
}

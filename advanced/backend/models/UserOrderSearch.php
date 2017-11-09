<?php

namespace backend\models;

use frontend\models\UserAddress;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UserOrderSearch represents the model behind the search form about `backend\models\UserOrder`.
 */
class UserOrderSearch extends UserOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'num', 'status'], 'integer'],
            [['user_id', 'out_trade_no', 'create_time', 'trade_no', 'pay_time', 'deliver_time', 'express_name', 'express_num','start_time','end_time'], 'safe'],
            [['order_money', 'fee_money'], 'number'],
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
        $query = UserOrder::find()->with('address');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'pay_time' => SORT_DESC,
                ]
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
            'user_id' => $this->user_id,
            'order_money' => $this->order_money,
            'num' => $this->num,
//            'fee_money' => $this->fee_money,
            'status' => $this->status,
//            'create_time' => $this->create_time,
//            'pay_time' => $this->pay_time,
//            'deliver_time' => $this->deliver_time,
        ]);

        $query->andFilterWhere(['between','pay_time',$this->start_time,$this->end_time])
            ->andFilterWhere(['like', 'out_trade_no', $this->out_trade_no])
            ->andFilterWhere(['like', 'trade_no', $this->trade_no])
            ->andFilterWhere(['like', 'express_name', $this->express_name])
            ->andFilterWhere(['like', 'express_num', $this->express_num]);

        return $dataProvider;
    }

}

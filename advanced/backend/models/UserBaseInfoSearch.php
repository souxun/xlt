<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\UserBaseInfo;

/**
 * UserBaseInfoSearch represents the model behind the search form about `frontend\models\UserBaseInfo`.
 */
class UserBaseInfoSearch extends UserBaseInfo
{
    public  $count;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sex', 'role_type','count'], 'integer'],
            [['user_id', 'openid', 'nickname', 'province', 'city', 'create_time', 'a_from', 'b_from', 'session_id', 'wx', 'phone'], 'safe'],

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
        $query = UserBaseInfo::find()->orderBy('create_time desc');

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
            'sex' => $this->sex,
            'create_time' => $this->create_time,
            'role_type' => $this->role_type,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'openid', $this->openid])
            ->andFilterWhere(['like', 'nickname', $this->nickname])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'a_from', $this->a_from])
            ->andFilterWhere(['like', 'b_from', $this->b_from])
            ->andFilterWhere(['like', 'session_id', $this->session_id])
            ->andFilterWhere(['like', 'wx', $this->wx])
            ->andFilterWhere(['like', 'phone', $this->phone]);

        return $dataProvider;
    }

    public function searchs($params)
    {
        $query = UserBaseInfo::find()->select(['a_from', 'count( * ) AS count '])->groupBy('a_from')->orderBy('count desc');

        // add conditions that should always apply here
//    var_dump($query);die;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'totalcount'=>100,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;

        }

      return $dataProvider;
    }
}

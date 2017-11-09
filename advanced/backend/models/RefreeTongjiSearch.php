<?php

namespace backend\models;

use frontend\models\UserBaseInfo;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\RefreeTongji;

/**
 * RefreeTongjiSearch represents the model behind the search form about `frontend\models\RefreeTongji`.
 */
class RefreeTongjiSearch extends RefreeTongji
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'subNum', 'payNum'], 'integer'],
            [['user_id', 'create_time'], 'safe'],
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
        $query = UserBaseInfo::find()->select('a_from')->groupBy('a_from');

//        $sql = 'SELECT r.subNum,r.payNum,u.a_from FROM refree_tongji as r,user_base_info u where r.user_id=u.user_id  order by u.a_from  desc';
//        $query = RefreeTongji::findBySql($sql)->all();
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
            'subNum' => $this->subNum,
            'payNum' => $this->payNum,
            'create_time' => $this->create_time,
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id]);

        return $dataProvider;
    }
}

<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "refree_settle_bill".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $yx_from
 * @property string $reward_money
 * @property string $real_reward_money
 * @property integer $settle_type
 * @property string $out_trade_no
 * @property string $remark
 * @property string $create_time
 */
class RefreeSettleBill extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'refree_settle_bill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reward_money', 'real_reward_money'], 'number'],
            [['settle_type'], 'integer'],
            [['remark'], 'string'],
            [['create_time'], 'safe'],
            [['user_id', 'yx_from'], 'string', 'max' => 30],
            [['out_trade_no'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键，自增',
            'user_id' => '用户id',
            'yx_from' => '推荐人用户id',
            'reward_money' => '奖励金额,结算到分',
            'real_reward_money' => '真实的奖励金额',
            'settle_type' => '结算类型:1A级推荐人2B级',
            'out_trade_no' => '订单号，唯一',
            'remark' => '备注',
            'create_time' => '创建时间',
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "people_money_exchange_bill".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $order_id
 * @property string $exchange_money
 * @property integer $exchange_type
 * @property integer $type
 * @property string $remark
 * @property string $create_time
 */
class PeopleMoneyExchangeBill extends \yii\db\ActiveRecord
{

    public $startdate;
    public $enddate;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'people_money_exchange_bill';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'order_id'], 'required'],
            [['exchange_money'], 'number','max'=>50],
            [['exchange_type', 'type'], 'integer'],
            [['remark'], 'string'],
            [['create_time'], 'safe'],
            [['user_id'], 'string', 'max' => 30],
            [['order_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'user_id' => '用户ID',
            'order_id' => '订单号',
            'exchange_money' => '兑换金额',
            'exchange_type' => '兑换类型1推荐人奖励',
            'type' => '类型1微信营销108招2宝典',
            'remark' => '备注',
            'create_time' => '创建时间',
        ];
    }
}

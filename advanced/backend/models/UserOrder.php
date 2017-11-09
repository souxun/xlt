<?php

namespace backend\models;

use frontend\models\UserAddress;

use Yii;

/**
 * This is the model class for table "user_order".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $out_trade_no
 * @property string $order_money
 * @property integer $num
 * @property string $fee_money
 * @property integer $status
 * @property string $create_time
 * @property string $trade_no
 * @property string $pay_time
 * @property string $deliver_time
 * @property string $express_name
 * @property string $express_num
 */
class UserOrder extends \yii\db\ActiveRecord
{
    public $start_time;
    public $end_time;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['order_money', 'fee_money'], 'number'],
            [['num', 'status'], 'integer'],
            [['create_time', 'pay_time', 'deliver_time','start_time', 'end_time'], 'safe'],
            [['user_id', 'express_name'], 'string', 'max' => 30],
            [['out_trade_no', 'trade_no'], 'string', 'max' => 100],
            [['express_num'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'out_trade_no' => '订单号',
            'order_money' => '订单金额',
            'num' => '商品数量',
            'fee_money' => '运费',
            'status' => '订单状态',
            'create_time' => '下单时间',
            'trade_no' => '微信交易号',
            'pay_time' => '支付时间',
            'deliver_time' => '发货时间',
            'express_name' => '快递公司',
            'express_num' => '快递单号',
            'start_time'=>'支付时间'
        ];
    }

    public function getAddress(){
        return $this->hasOne(UserAddress::className(),['user_id'=>'user_id']);
    }
}

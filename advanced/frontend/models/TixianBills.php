<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "tixian_bills".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $nickname
 * @property string $money
 * @property integer $type
 * @property string $account
 * @property integer $status
 * @property string $create_time
 */
class TixianBills extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tixian_bills';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['money'], 'number'],
            [['type', 'status'], 'integer'],
            [['create_time'], 'safe'],
            [['user_id', 'account', 'phone'], 'string', 'max' => 30],
            [['nickname'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'user_id' => '用户id',
            'nickname' => '昵称',
            'money' => '佣金金额',
            'type' => '提现方式1支付宝',
            'account' => '提现账号',
            'status' => '状态0未确认1已确认',
            'phone' => '手机号',
            'create_time' => '创建时间',
        ];
    }
}

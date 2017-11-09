<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "people_user_address".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $name
 * @property string $phone
 * @property string $fix_tel
 * @property string $zip_code
 * @property string $province
 * @property string $city
 * @property string $country
 * @property string $detail_address
 * @property string $create_time
 */
class PeopleUserAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'people_user_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['create_time'], 'safe'],
            [['user_id'], 'string', 'max' => 30],
            [['name', 'phone', 'fix_tel', 'zip_code', 'province', 'city', 'country'], 'string', 'max' => 100],
            [['detail_address'], 'string', 'max' => 300],
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
            'name' => '收货人姓名',
            'phone' => '手机号码',
            'fix_tel' => '固定电话',
            'zip_code' => '邮政编码',
            'province' => '省份',
            'city' => '城市',
            'country' => '地区',
            'detail_address' => '详细地址',
            'create_time' => '创建时间',
        ];
    }
}

<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_base_info".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $openid
 * @property string $nickname
 * @property integer $sex
 * @property string $province
 * @property string $city
 * @property string $create_time
 * @property integer $role_type
 * @property string $a_from
 * @property string $b_from
 * @property string $session_id
 * @property string $wx
 * @property string $phone
 */
class UserBaseInfo extends \yii\db\ActiveRecord
{
    public  $count;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_base_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['user_id'], 'required'],
            [['sex','role_type','count'], 'integer'],
            [['create_time'], 'safe'],
            [['user_id','a_from','b_from','phone'], 'string', 'max' => 30],
            [['openid'], 'string', 'max' => 50],
            [['nickname'], 'string', 'max' => 255],
            [['session_id'], 'string', 'max' => 300],
            [['wx'], 'string', 'max' => 100],
            [['province', 'city'], 'string', 'max' => 5],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键自增',
            'user_id' => '用户id',
            'openid' => '公众号openid',
            'nickname' => '昵称',
            'sex' => '性别',
            'province' => '省份',
            'city' => '城市',
            'create_time' => '创建时间',
            'role_type' => '角色0无1会员',
            'a_from' => '一级推荐人',
            'b_from' => '二级推荐人',
            'session_id' => 'session_id',
            'wx' => '微信',
            'phone' => '手机号',
        ];
    }

    public function getProvince_(){
        return $this->hasOne(TblCity::className(),['id'=>'province'])->select(['id','name']);
    }

    public function getCity_(){
        return $this->hasOne(TblCity::className(),['id'=>'city'])->select(['id','name']);
    }

    public function getTongji(){
        return $this->hasOne(RefreeTongji::className(),['user_id'=>'user_id'])->select(['user_id','subNum','payNum']);
    }


//    后台地址方法
    public function getProvinces(){
        return $this->hasOne(UserAddress::className(),['user_id'=>'user_id'])->select(['id','province','city','country','detail_address']);
    }

}


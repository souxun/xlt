<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "people_user_detail_info".
 *
 * The followings are the available columns in table 'people_user_detail_info':
 * @property integer $id
 * @property string $user_id
 * @property string $name
 * @property string $phone
 * @property string $weixin
 * @property string $home
 * @property string $profession
 * @property string $company_name
 * @property string $operating_range
 * @property integer $income
 * @property string $signature
 * @property string $identification
 * @property string $address
 * @property string $create_time
 */
class UserDetailInfo extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public  static function tableName()
	{
		return 'people_user_detail_info';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('income', 'numerical', 'integerOnly'=>true),
			array('user_id, phone', 'length', 'max'=>30),
			array('phone', 'match', 'pattern'=>'/^1[3,4,5,7,8]\d{9}$/','message'=>'请填写正确的手机号码！'),
			array('name, weixin, home, profession, company_name', 'length', 'max'=>100),
			array('operating_range', 'length', 'max'=>300),
			array('identification, address', 'length', 'max'=>100),
			array('identification', 'match', 'pattern'=>'/^[1-9][0-9]{14}$|^[1-9][0-9]{16}[0-9a-zA-Z]$/','message'=>'请填写正确的身份证号码！'),
			array('signature, create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, name, weixin, phone, home, profession, company_name, operating_range, income, signature, identification, address, create_time', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => '用户ID',
			'name' => '姓名',
			'phone' => '手机号',
			'weixin' => '微信号',
			'home' => '常住地址',
			'profession' => '职业',
			'company_name' => '企业名称',
			'operating_range' => '经营范围',
			'income' => '年营业额',
			'signature' => '个人签名',
			'identification' => '身份证',
			'address' => '收货地址',
			'create_time' => '创建时间',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('phone',$this->phone,true);
		$criteria->compare('home',$this->home,true);
		$criteria->compare('profession',$this->profession,true);
		$criteria->compare('company_name',$this->company_name,true);
		$criteria->compare('operating_range',$this->operating_range,true);
		$criteria->compare('income',$this->income);
		$criteria->compare('signature',$this->signature,true);
		$criteria->compare('identification',$this->identification,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserDetailInfo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

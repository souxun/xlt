<?php
namespace app\models;

use Yii;
/**
 * This is the model class for table "people_user_info_view".
 *
 * The followings are the available columns in table 'people_user_info_view':
 * @property string $user_id
 * @property string $nickname
 * @property integer $sex
 * @property integer $role_type
 * @property string $province
 * @property string $city
 * @property string $real_name
 * @property string $phone
 * @property string $weixin
 * @property string $a_from
 * @property string $b_from
 * @property string $c_from
 * @property integer $has_wx_code
 * @property integer $wx_off
 * @property integer $is_private
 */
class UserInfoView extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public  static   function tableName()
	{
		return 'people_user_info_view';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('nickname', 'required'),
			array('sex, role_type, has_wx_code, wx_off, is_private', 'numerical', 'integerOnly'=>true),
			array('user_id, phone, a_from, b_from, c_from', 'length', 'max'=>30),
			array('real_name, weixin', 'length', 'max'=>100),
			array('nickname', 'length', 'max'=>255),
			array('province, city', 'length', 'max'=>20),
			array('create_time','safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('user_id, nickname, sex, role_type, has_wx_code, wx_off, province, city, real_name, phone, weixin, a_from, b_from, c_from, create_time', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'nickname' => 'Nickname',
			'sex' => 'Sex',
			'role_type' => 'Role Type',
			'has_wx_code' => 'has_wx_code',
			'province' => 'Province',
			'city' => 'City',
			'phone' => 'Phone',
			'a_from' => 'a_from',
			'b_from' => 'b_from',
			'c_from' => 'c_from',
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

		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('sex',$this->sex);
		$criteria->compare('role_type',$this->role_type);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('phone',$this->phone,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserInfoView the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

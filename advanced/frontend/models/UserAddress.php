<?php
namespace frontend\models;

use Yii;
/**
 * This is the model class for table "people_user_address".
 *
 * The followings are the available columns in table 'people_user_address':
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
class UserAddress extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public  static function tableName()
	{
		return 'people_user_address';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		/*return array(
			array('user_id', 'required'),
			array('user_id', 'length', 'max'=>30),
			array('name, phone, fix_tel, zip_code, province, city, country', 'length', 'max'=>100),
			array('detail_address', 'length', 'max'=>300),
			array('create_time','safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, name, phone, fix_tel, zip_code, province, city, country, detail_address, create_time', 'safe', 'on'=>'search'),
		);*/

		 return [
            [['user_id'], 'required'],
            [['create_time'], 'safe'],
            [['user_id'], 'string', 'max' => 30],
            [['name', 'phone', 'fix_tel', 'zip_code', 'province', 'city', 'country'], 'string', 'max' => 100],
            [['detail_address'], 'string', 'max' => 300],
        ];
	}

	/**
	 * @return array relational rules.
	 */
	// public function relations()
	// {
	// 	// NOTE: you may need to adjust the relation name and the related
	// 	// class name for the relations automatically generated below.
	// 	return array(
	// 	);
	// }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => '用户ID',
			'name' => '收货人姓名',
			'phone' => '手机',
			'fix_tel' => '固定电话',
			'zip_code' => '邮政编码',
			'province' => '省份',
			'city' => '城市',
			'country' => '地区',
			'detail_address' => '详细地址',
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
		$criteria->compare('fix_tel',$this->fix_tel,true);
		$criteria->compare('zip_code',$this->zip_code,true);
		$criteria->compare('province',$this->province,true);
		$criteria->compare('city',$this->city,true);
		$criteria->compare('country',$this->country,true);
		$criteria->compare('detail_address',$this->detail_address,true);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserAddress the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

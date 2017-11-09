<?php

/**
 * This is the model class for table "people_user_pics".
 *
 * The followings are the available columns in table 'people_user_pics':
 * @property integer $id
 * @property string $user_id
 * @property string $pic_name
 * @property integer $is_first
 * @property string $create_time
 * @property int $status
 * @property string $confirm_time
 */
class UserPics extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'people_user_pics';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('is_first', 'numerical', 'integerOnly'=>true),
			array('user_id', 'length', 'max'=>30),
			array('pic_name', 'length', 'max'=>50),
			array('create_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, pic_name,is_first, create_time', 'safe', 'on'=>'search'),
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
			'user_id' => 'User',
			'pic_name' => 'Pic Name',
			'is_first' => 'Is First',
			'create_time' => 'Create Time',
			'status' => 'status',
			'confirm_time' => 'confirm_time'				
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
		$criteria->compare('pic_name',$this->pic_name,true);
		$criteria->compare('is_first',$this->is_first);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('status',$this->status,true);
		$criteria->compare('confirm_time',$this->confirm_time,true);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserPics the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	public function statusZero()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
	
		$criteria=new CDbCriteria;
	
		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('pic_name',$this->pic_name,true);
		$criteria->compare('is_first',$this->is_first);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('status',0);
		$criteria->compare('confirm_time',$this->confirm_time,true);
	
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
		));
	}
	public function statusOne()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
	
		$criteria=new CDbCriteria;
	
		$criteria->compare('id',$this->id);
		$criteria->compare('user_id',$this->user_id,true);
		$criteria->compare('pic_name',$this->pic_name,true);
		$criteria->compare('is_first',$this->is_first);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('status',1);
		$criteria->compare('confirm_time',$this->confirm_time,true);
	
		return new CActiveDataProvider($this, array(
				'criteria'=>$criteria,
		));
	}
	
}

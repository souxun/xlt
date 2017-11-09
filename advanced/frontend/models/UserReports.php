<?php

/**
 * This is the model class for table "people_user_reports".
 *
 * The followings are the available columns in table 'people_user_reports':
 * @property integer $id
 * @property string $user_id
 * @property string $nickname
 * @property string $weixin
 * @property string $reason
 * @property string $tel
 * @property string $create_time
 * @property string $goods_id
 */
class UserReports extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'people_user_reports';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tel, create_time, goods_id', 'required'),
			array('user_id, goods_id', 'length', 'max'=>30),
			array('nickname, weixin', 'length', 'max'=>255),
			array('tel', 'length', 'max'=>50),
			array('reason', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_id, nickname, weixin, reason, tel, create_time, goods_id', 'safe', 'on'=>'search'),
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
			'id' => '主键，自增',
			'user_id' => '用户id',
			'nickname' => '姓名',
			'weixin' => '微信号',
			'reason' => '举报的原因',
			'tel' => '电话',
			'create_time' => '创建时间',
			'goods_id' => '微货id',
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
		$criteria->compare('nickname',$this->nickname,true);
		$criteria->compare('weixin',$this->weixin,true);
		$criteria->compare('reason',$this->reason,true);
		$criteria->compare('tel',$this->tel,true);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('goods_id',$this->goods_id,true);
		$criteria->order = 'create_time DESC';
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserReports the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

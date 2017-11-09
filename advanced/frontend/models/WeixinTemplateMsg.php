<?php

/**
 * This is the model class for table "weixin_template_msg".
 *
 * The followings are the available columns in table 'weixin_template_msg':
 * @property integer $msg_id
 * @property string $openid
 * @property string $template_id
 * @property string $send_time
 */
class WeixinTemplateMsg extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'weixin_template_msg';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('openid', 'length', 'max'=>128),
			array('template_id', 'length', 'max'=>64),
			array('send_time', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('msg_id, openid, template_id, send_time', 'safe', 'on'=>'search'),
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
			'msg_id' => 'Msg',
			'openid' => 'Openid',
			'template_id' => 'Template',
			'send_time' => 'Send Time',
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

		$criteria->compare('msg_id',$this->msg_id);
		$criteria->compare('openid',$this->openid,true);
		$criteria->compare('template_id',$this->template_id,true);
		$criteria->compare('send_time',$this->send_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WeixinTemplateMsg the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

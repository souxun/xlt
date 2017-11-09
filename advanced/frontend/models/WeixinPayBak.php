<?php

/**
 * This is the model class for table "weixin_pay_bak".
 *
 * The followings are the available columns in table 'weixin_pay':
 * @property integer $id
 * @property string $appid
 * @property string $mch_id
 * @property string $device_info
 * @property string $nonce_str
 * @property string $sign
 * @property string $result_code
 * @property string $openid
 * @property integer $is_subscribe
 * @property string $trade_type
 * @property string $bank_type
 * @property integer $total_fee
 * @property integer $coupon_fee
 * @property string $fee_type
 * @property string $transaction_id
 * @property string $out_trade_no
 * @property string $attach
 * @property string $time_end
 * @property string $timestamp
 * @property integer $pay_type
 */
class WeixinPayBak extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'weixin_pay_bak';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('appid, mch_id, nonce_str, sign, result_code, openid, total_fee, transaction_id, out_trade_no, time_end', 'required'),
			array('total_fee, coupon_fee, pay_type', 'numerical', 'integerOnly'=>true),
			array('appid, mch_id, device_info, nonce_str, sign, transaction_id', 'length', 'max'=>32),
			array('is_subscribe', 'length', 'max'=>10),
			array('result_code, bank_type', 'length', 'max'=>16),
			array('openid, attach', 'length', 'max'=>128),
			array('trade_type, fee_type', 'length', 'max'=>8),
			array('out_trade_no', 'length', 'max'=>100),
			array('time_end', 'length', 'max'=>14),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, appid, mch_id, device_info, nonce_str, sign, result_code, openid, is_subscribe, trade_type, bank_type, total_fee, coupon_fee, fee_type, transaction_id, out_trade_no, attach, time_end, timestamp, pay_type', 'safe', 'on'=>'search'),
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
			'appid' => 'Appid',
			'mch_id' => 'Mch',
			'device_info' => 'Device Info',
			'nonce_str' => 'Nonce Str',
			'sign' => 'Sign',
			'result_code' => 'Result Code',
			'openid' => 'Openid',
			'is_subscribe' => 'Is Subscribe',
			'trade_type' => 'Trade Type',
			'bank_type' => 'Bank Type',
			'total_fee' => 'Total Fee',
			'coupon_fee' => 'Coupon Fee',
			'fee_type' => 'Fee Type',
			'transaction_id' => 'Transaction',
			'out_trade_no' => 'Out Trade No',
			'attach' => 'Attach',
			'time_end' => 'Time End',
			'timestamp' => 'Timestamp',
			'pay_type' => 'Pay Type',
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
		$criteria->compare('appid',$this->appid,true);
		$criteria->compare('mch_id',$this->mch_id,true);
		$criteria->compare('device_info',$this->device_info,true);
		$criteria->compare('nonce_str',$this->nonce_str,true);
		$criteria->compare('sign',$this->sign,true);
		$criteria->compare('result_code',$this->result_code,true);
		$criteria->compare('openid',$this->openid,true);
		$criteria->compare('is_subscribe',$this->is_subscribe);
		$criteria->compare('trade_type',$this->trade_type,true);
		$criteria->compare('bank_type',$this->bank_type,true);
		$criteria->compare('total_fee',$this->total_fee);
		$criteria->compare('coupon_fee',$this->coupon_fee);
		$criteria->compare('fee_type',$this->fee_type,true);
		$criteria->compare('transaction_id',$this->transaction_id,true);
		$criteria->compare('out_trade_no',$this->out_trade_no,true);
		$criteria->compare('attach',$this->attach,true);
		$criteria->compare('time_end',$this->time_end,true);
		$criteria->compare('timestamp',$this->timestamp,true);
		$criteria->compare('pay_type',$this->pay_type);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return WeixinPay the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

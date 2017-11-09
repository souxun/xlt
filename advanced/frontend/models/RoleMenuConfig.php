<?php

/**
 * This is the model class for table "people_role_menu_config".
 *
 * The followings are the available columns in table 'people_role_menu_config':
 * @property integer $config_id
 * @property integer $role_id
 * @property integer $menu_id
 * @property string $create_time
 *
 * The followings are the available model relations:
 * @property MyMenu $menu
 * @property MyOpRoles $role
 */
class RoleMenuConfig extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return RoleMenuConfig the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'people_role_menu_config';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('role_id, menu_id, create_time', 'required'),
			array('role_id, menu_id', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('config_id, role_id, menu_id, create_time', 'safe', 'on'=>'search'),
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
			'menu' => array(self::BELONGS_TO, 'Menu', 'menu_id'),
			'role' => array(self::BELONGS_TO, 'OpRoles', 'role_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'config_id' => 'Config',
			'role_id' => 'Role',
			'menu_id' => 'Menu',
			'create_time' => 'Create Time',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('config_id',$this->config_id);
		$criteria->compare('role_id',$this->role_id);
		$criteria->compare('menu_id',$this->menu_id);
		$criteria->compare('create_time',$this->create_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}
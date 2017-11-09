<?php

/**
 * This is the model class for table "people_menu".
 *
 * The followings are the available columns in table 'people_menu':
 * @property integer $menu_id
 * @property string $menu_name
 * @property integer $menu_level
 * @property integer $parent_menu_id
 * @property string $action_url
 * @property integer $is_leaf_menu
 * @property integer $valid
 * @property string $create_time
 * @property string $update_time
 *
 * The followings are the available model relations:
 * @property MyRoleMenuConfig[] $myRoleMenuConfigs
 */
class Menu extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return Menu the static model class
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
		return 'people_menu';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('menu_id, menu_name, menu_level, is_leaf_menu, create_time', 'required'),
			array('menu_id, menu_level, parent_menu_id, is_leaf_menu, valid', 'numerical', 'integerOnly'=>true),
			array('menu_name', 'length', 'max'=>60),
			array('action_url', 'length', 'max'=>128),
			array('update_time', 'safe'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('menu_id, menu_name, menu_level, parent_menu_id, action_url, is_leaf_menu, valid, create_time, update_time', 'safe', 'on'=>'search'),
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
			'menu_id' => '菜单ID',
			'menu_name' => '菜单名称',
			'menu_level' => 'Menu Level',
			'parent_menu_id' => 'Parent Menu',
			'action_url' => 'Action Url',
			'is_leaf_menu' => 'Is Leaf Menu',
			'valid' => 'Valid',
			'create_time' => 'Create Time',
			'update_time' => 'Update Time',
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

		$criteria->compare('menu_id',$this->menu_id);
		$criteria->compare('menu_name',$this->menu_name,true);
		$criteria->compare('menu_level',$this->menu_level);
		$criteria->compare('parent_menu_id',$this->parent_menu_id);
		$criteria->compare('action_url',$this->action_url,true);
		$criteria->compare('is_leaf_menu',$this->is_leaf_menu);
		$criteria->compare('valid',$this->valid);
		$criteria->compare('create_time',$this->create_time,true);
		$criteria->compare('update_time',$this->update_time,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * 依据用户role获取菜单树
	 */
	 public function getMenuTreeByRole($role)
	 {
	 	//获取父菜单
	 	$parentMenuSql = "select menu_id, menu_name, menu_icon, action_url from people_menu
				where menu_id in (
									select distinct menu_id from people_menu
									where menu_id in (select menu_id from people_role_menu_config where role_id =:role)
									and menu_level = 0
									and valid = 1
									union all
									select distinct parent_menu_id from people_menu
									where menu_id in (select menu_id from people_role_menu_config where role_id =:role)
									and menu_level = 1
									and valid = 1
								) and menu_id !=600";
				
		$childMenuSql = "select menu_id, menu_name, menu_icon, action_url from people_menu
						where menu_id in (
											SELECT menu_id
											FROM people_role_menu_config
											where role_id =:role
											and parent_menu_id = :parent_menu_id
											and valid = 1
										)
						and menu_level = 1";
										
		$connection = Yii::app()->db;
		$parentCommand = $connection->createCommand($parentMenuSql);
		$parentCommand->bindParam(":role", $role, PDO::PARAM_INT);
		$dataReader = $parentCommand->query();
		
		$childCommand = $connection->createCommand($childMenuSql);
		$childCommand->bindParam(":role", $role, PDO::PARAM_INT);
		
		$menuTree = array();
		foreach ($dataReader as $k => $row)
		{
			$parentMenu = array();
			$parentMenu['label'] = $row['menu_name'];
			$parentMenu['icon'] = $row['menu_icon'];
			$parentMenu['url'] = isset($row['action_url']) ? Yii::app()->createUrl($row['action_url']) : '#';
			$parentMenu['items'] = array();
			if ($k === 0)
				$parentMenu['active'] = true;
			
			$childCommand->bindParam(":parent_menu_id", $row['menu_id'], PDO::PARAM_INT);
			$subDataReader = $childCommand->query();
			
			foreach ($subDataReader as $key => $subRow) 
			{
				$childMenu = array();
				$childMenu['label'] = $subRow['menu_name'];
				$childMenu['icon'] = $subRow['menu_icon'];
				$childMenu['url'] = isset($subRow['action_url']) ? Yii::app()->createUrl($subRow['action_url']) : '#';

				array_push($parentMenu['items'], $childMenu);	
				
			}
			//var_dump($parentMenu['items']);
			array_push($menuTree, $parentMenu);
		}
		
		//var_dump($menuTree);
		return $menuTree;
	 }
}
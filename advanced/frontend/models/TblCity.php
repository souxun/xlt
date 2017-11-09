<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "tbl_city".
 *
 * @property integer $id
 * @property integer $pid
 * @property string $name
 * @property string $pingyin
 */
class TblCity extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_city';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pid', 'name'], 'required'],
            [['pid'], 'integer'],
            [['name'], 'string', 'max' => 20],
            [['pingyin'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pid' => 'Pid',
            'name' => 'Name',
            'pingyin' => 'pingyin',
        ];
    }
	
	public function findCity($name)
	{
		if($name == '') return ['id' => 0];
		
		$result = $this::find()
				->select(['id'])
				->where('name like :name')
				->addParams([':name'=>'%'.$name.'%'])
				->one();
		if(empty($result))
			return ['id' => 0];
		
		return ['id' => $result->id];	
	}
	
	public function findCityPing($pingyin)
	{
		if($pingyin == '') return ['id' => 0];
		
		$result = $this::find()
				->select(['id'])
				->where('pingyin=:pingyin',[':pingyin'=>$pingyin])
				->one();
		
		if(empty($result))
			return ['id' => 0];
		
		return ['id' => $result->id];	
	}	
	
	public function getCityName($id)
	{
		$cityModel = $this::findOne($id);
		if(!empty($cityModel)){
			return $cityModel->name;
		}else{
			return "未知";
		}
	}
	
	public function getProvinces() {
		$provinces = $this::find()->where("pid=0")->all();
		return $provinces;
	}
	
	public function getCitys($pid) {
		$citys = $this::find()->where("pid=:pid", [":pid"=>$pid])->all();
		return $citys;
	}	
}

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/29 0029
 * Time: 上午 11:11
 */

namespace frontend\models;


use yii\db\ActiveRecord;

class XltData extends ActiveRecord
{
    public static function tableName()
    {
        return 'xlt_data';
    }

    public function rules()
    {
        return [
            [['name', 'phone','user_id'], 'required',],
            [['name',], 'string','max'=>'20'],
            [['phone','user_id'], 'string','max'=>'30'],
            [['phone',], 'match','pattern'=>'/^1\d{10}$/','message'=>'手机号码格式不正确'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => '主键自增',
            'user_id' => '用户id',
            'name'=>'姓名',
            'phone' => '手机号',
            'create_time' => '创建时间',
        ];
    }
}
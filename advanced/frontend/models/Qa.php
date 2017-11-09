<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/28 0028
 * Time: 上午 9:13
 */

namespace frontend\models;


use yii\db\ActiveRecord;

class Qa extends ActiveRecord
{
    public static function tableName()
    {
        return 'people_qa';
    }

    public static function getQaAll(){
        $qa=self::find()->select(['question','answer'])->asArray()->all();
        return $qa;
    }
}
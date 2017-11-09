<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "refree_tongji".
 *
 * @property integer $id
 * @property string $user_id
 * @property integer $subNum
 * @property integer $payNum
 * @property string $create_time
 */
class RefreeTongji extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'refree_tongji';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subNum', 'payNum'], 'integer'],
            [['create_time'], 'safe'],
            [['user_id'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'subNum' => 'Sub Num',
            'payNum' => 'Pay Num',
            'create_time' => 'Create Time',
        ];
    }
}

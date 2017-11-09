<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_wxcode_info".
 *
 * @property integer $id
 * @property string $user_id
 * @property string $media_id
 * @property string $expire_time
 */
class WxcodeInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_wxcode_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['user_id','media_id','expire_time'], 'required'],
            [['expire_time'], 'safe'],
            [['user_id'], 'string', 'max' => 30],
            [['media_id'], 'string', 'max' => 100],
            [['user_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键自增',
            'user_id' => '用户id',
            'media_id' => '多媒体ID',
            'expire_time' => '过期时间',
        ];
    }
}

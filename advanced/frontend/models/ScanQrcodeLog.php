<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "user_wxcode_info".
 *
 * @property integer $id
 * @property string $openid
 * @property string $yx_from
 * @property string $qrcode_type
 * @property string $create_time
 */
class ScanQrcodeLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'scan_qrcode_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			[['openid','yx_from'], 'required'],
            [['expire_time'], 'safe'],
            [['openid'], 'string', 'max' => 50],
            [['yx_from'], 'string', 'max' => 30],
            [['qrcode_type'], 'string', 'max' => 13],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '主键自增',
			'openid' => 'Openid',
			'yx_from' => 'Yx From',
			'qrcode_type' => 'Qrcode Type',
			'create_time' => 'Create Time',
        ];
    }
}

<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "weixin_post_log".
 *
 * @property integer $id
 * @property string $fromuser
 * @property string $msgtype
 * @property string $event
 * @property string $eventkey
 * @property string $content
 * @property string $picUrl
 * @property string $format
 * @property string $mediaId
 * @property string $location_x
 * @property string $location_y
 * @property string $scale
 * @property string $label
 * @property string $create_time
 */
class WeixinPostLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'weixin_post_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fromuser', 'msgtype'], 'required'],
            [['eventkey'], 'string'],
            [['create_time'], 'safe'],
            [['fromuser'], 'string', 'max' => 30],
            [['msgtype', 'location_x', 'location_y', 'scale'], 'string', 'max' => 10],
            [['event'], 'string', 'max' => 15],
            [['content'], 'string', 'max' => 255],
            [['picUrl'], 'string', 'max' => 100],
            [['format'], 'string', 'max' => 20],
            [['mediaId'], 'string', 'max' => 60],
            [['label'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'fromuser' => 'from用户',
            'msgtype' => '类型',
            'event' => '事件',
            'eventkey' => '事件key',
            'content' => '内容',
            'picUrl' => '图片URL',
            'format' => '格式',
            'mediaId' => '媒体ID，接口可以用',
            'location_x' => '地理纬度',
            'location_y' => '地理经度',
            'scale' => '缩放大小',
            'label' => '地理位置信息',
            'create_time' => 'Create Time',
        ];
    }
}

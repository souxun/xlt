<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "people_qa".
 *
 * @property integer $id
 * @property string $question
 * @property string $answer
 * @property string $create_time
 */
class Qa extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'people_qa';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question', 'answer'], 'required'],
            [['answer'], 'string'],
            [['create_time'], 'safe'],
            [['question'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'question' => 'Question',
            'answer' => 'Answer',
            'create_time' => 'Create Time',
        ];
    }
}

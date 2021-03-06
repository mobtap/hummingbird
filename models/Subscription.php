<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "subscription".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $report_id
 * @property integer $deleted
 * @property string  $created_at
 * @property string  $updated_at
 *
 * @property Report $report
 */
class Subscription extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subscription';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'report_id'], 'required'],
            [['user_id', 'report_id', 'deleted'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['report_id'],
                'exist',
                'skipOnError'     => true,
                'targetClass'     => Report::className(),
                'targetAttribute' => ['report_id' => 'id']
            ],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'user_id'    => Yii::t('app', 'User'),
            'report_id'  => Yii::t('app', 'Report'),
            'deleted'    => Yii::t('app', 'Deleted'),
            'created_at' => Yii::t('app', 'Subscribed At'),
            'updated_at' => Yii::t('app', 'Updated'),
        ];
    }

    public static function subscribe($user_id, $report_id)
    {
        $subscription = self::findOne([
              'user_id'   => $user_id,
              'report_id' => $report_id,
        ]);
        if (!$subscription) {
            $subscription            = new self();
            $subscription->loadDefaultValues();
            $subscription->user_id   = $user_id;
            $subscription->report_id = $report_id;
            $subscription->save();
        }
        return $subscription;
    }

    /**
     * Check if a user has already subscribed a report
     * @param integer $user_id
     * @param integer $report_id
     * @return boolean
     */
    public static function isSubscribed($user_id, $report_id)
    {
        return self::find()->where([
                    'user_id' => $user_id,
                    'report_id' => $report_id,
                ])->exists();
    }

    public static function unsubscribe($user_id, $report_id)
    {
        $subscription = self::findOne([
              'user_id'   => $user_id,
              'report_id' => $report_id,
        ]);
        if ($subscription) {
            $subscription->delete();
        }
        return true;
    }

    /**
     * @return ActiveQuery
     */
    public function getReport()
    {
        return $this->hasOne(Report::className(), ['id' => 'report_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return SubscriptionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new SubscriptionQuery(get_called_class());
    }
}

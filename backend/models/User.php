<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property integer $role
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $avatar
 *
 * @property ForumBoard[] $forumBoards
 * @property UserMessage[] $userMessages
 * @property UserProfile $userProfile
 */
class User extends \yii\db\ActiveRecord
{
    public $new_password;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'status', 'created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 32],
            [['password_hash', 'new_password', 'password_reset_token', 'auth_key'], 'string', 'max' => 255],
            [['email'], 'string', 'max' => 64],
            [['avatar'], 'string', 'max' => 24]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'new_password' => Yii::t('app', 'New Password'),
            'password_reset_token' => Yii::t('app', 'Password Reset Token'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'role' => Yii::t('app', 'Role'),
            'email' => Yii::t('app', 'Email'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'avatar' => Yii::t('app', 'Avatar'),
        ];
    }
    
    
    /**
     * This is invoked before the record is saved.
     * @return boolean whether the record should be saved.
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                if(isset($this->new_password)){
                    $this->password_hash = Yii::$app->security->generatePasswordHash($this->new_password);
                }
                $this->auth_key = Yii::$app->security->generateRandomString();
                $this->created_at = time();
                $this->updated_at = time();
                $this->status = 1;
            }
            return true;
        } else {
            return false;
        }
    }
    

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {   
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * @desc 转化时间格式
     * @param 时间戳格式  $datetime
     * @return 返回 2016-03-02 03:07:49 这种格式
     */
    public function convertDate($datetime){
        return date('Y-m-d H:i:s',$datetime);
    }
}

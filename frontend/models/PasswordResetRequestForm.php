<?php
/**
 * @link http://www.iisns.com/
 * @copyright Copyright (c) 2015 iiSNS
 * @license http://www.iisns.com/license/
 */
 
namespace frontend\models;

use common\models\User;
use yii\base\Model;

/**
 * Password reset request form
 *
 * @author Shiyang <dr@shiyang.me>
 */
class PasswordResetRequestForm extends Model
{
    public $user_email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['user_email', 'filter', 'filter' => 'trim'],
            ['user_email', 'required'],
            ['user_email', 'email'],
            ['user_email', 'exist',
                'targetClass' => '\common\models\User',
                'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => 'There is no user with such email.'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_email' => \Yii::t('app', 'Email'),
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'user_status' => User::STATUS_ACTIVE,
            'user_email' => $this->user_email,
        ]);

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            if ($user->save()) {
                return \Yii::$app->mailer->compose('passwordResetToken', ['user_user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name . ' robot'])
                    ->setTo($this->user_email)
                    ->setSubject('Password reset for ' . \Yii::$app->name)
                    ->send();
            }
        }

        return false;
    }
}

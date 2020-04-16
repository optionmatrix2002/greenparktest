<?php

namespace app\models;

use yii\base\Model;
use app\models\User;

// use kartik\password\StrengthValidator;

/**
 * Signup form
 */
class ChangePasswordForm extends Model
{

    public $email;

    public $currentPassword;

    public $newPassword;

    public $retypePassword;

    public $username;

    private $_user = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [
                'email',
                'trim'
            ],
            [
                'email',
                'required'
            ],
            [
                'email',
                'email'
            ],
            [
                'email',
                'string',
                'max' => 255
            ],
            [
                'email',
                'exist',
                'targetClass' => '\app\models\User',
                'filter' => [
                    'is_active' => 1
                ],
                'message' => 'There is no user with such email.'
            ],

            [
                'currentPassword',
                'required'
            ],
            [
                'currentPassword',
                'validateCurrentPassword'
            ],
            [
                'currentPassword',
                'string'
            ],

            [
                'newPassword',
                'required'
            ],
            // ['newPassword', StrengthValidator::className(), 'preset'=>'normal','min'=>8, 'digit'=>1,'upper' => 1, 'special'=>1],
            [
                'retypePassword',
                'required'
            ],
            [
                'newPassword',
                'string',
                'min' => 8
            ],
            [
                'retypePassword',
                'compare',
                'compareAttribute' => 'newPassword'
            ]
        ];
    }

    public function validateCurrentPassword($attribute)
    {
        $user = $this->getUser();
        if (!$user || !$user->validatePassword($this->$attribute)) {
            $this->addError('currentPassword', 'Incorrect password.');
        }
    }

    /**
     * @return bool
     */
    public function changePassword()
    {
        if ($this->validate() && !$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->currentPassword)) {
                $this->addError('currentPassword', 'Incorrect password.');
            } else {
                $user->setPassword($this->newPassword);
                $user->save();
                return true;
            }
        }
        return false;
    }

    /**
     * Finds user by [[email]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->email);
        }
        return $this->_user;
    }
}

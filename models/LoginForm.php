<?php

namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{

    public $username;

    public $password;

    public $rememberMe = true;

    public $confirmPassword;

    private $_user = false;

    // const SAVE_PASSWORD = 'savePassword';

    /**
     *
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [
                [
                    'username',
                    'password'
                ],
                'required'
            ],
            [
                [
                    'username'
                ],
                'email'
            ],
            // rememberMe must be a boolean value
            /* [
                 'rememberMe',
                 'boolean'
             ],*/
            // password is validated by validatePassword()
            [
                'password',
                'validatePassword'
            ],
            // password and confirm password required for saving
            [
                [
                    'confirmPassword'
                ],
                'required',
                'on' => 'savePassword'
            ],
            // password and confirm password compare
            [
                'confirmPassword',
                'compare',
                'compareAttribute' => 'password'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Password')
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute
     *            the attribute currently being validated
     * @param array $params
     *            the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getActiveUser();
            if (!$user) {
                $this->addError($attribute, 'Email seems wrong or not registered.');
            } elseif ($user->is_email_verified == 0) {
                $this->addError($attribute, 'Email verification not completed. Please verify.');
            } elseif (!$user->is_active) {
                $this->addError($attribute, 'User is inactive, please contact admin.');
            } else if (!$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect password');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 3 : 0);
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername(trim($this->username));
        }

        return $this->_user;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getActiveUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findOne([
                'email' => strtolower(trim($this->username)),
                'is_deleted' => 0
            ]);
        }

        return $this->_user;
    }
}
<?php
namespace common\models\forms;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Login form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $confirm_password;

    private $user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'email', 'confirm_password', 'password'], 'required'],
            ['email', 'email'],
            //['username', 'skipOnEmpty' => false],
            ['password', 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'message' => "Passwords don't match"],
            ['email', 'validateEmail'],
            ['username', 'validateUsername'],
            ['name', 'string']
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function signup()
    {
        if ($this->validate()) {
            $this->user = new User([
                'username' => $this->username,
                'name' => $this->name,
                'email' => $this->email,
                'status' => User::STATUS_ACTIVE
            ]);
            $this->user->setPassword($this->password);
            $this->user->generateAuthKey();
            $this->user->generatePasswordResetToken();
            if ($this->user->save()) {
                return $this->getAccessToken($this->user->id);
            }
        }
        return false;
    }

    public function validateEmail(){
        return !User::find()->where(['email' => $this->email])->exists();
    }

    public function validateUsername(){
        return !User::find()->where(['username' => $this->username])->exists();
    }

    protected function getAccessToken($user_id) {
        $module = Yii::$app->modules["oauth2"];
        $server = $module->getServer();
        return $server->createAccessToken("testclient", $user_id);
    }
}

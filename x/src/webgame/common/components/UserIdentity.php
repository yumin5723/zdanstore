<?php
/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    //const ERROR_EMAIL_INVALID = 3;
    public $user;

    public $id;

    /**
     * Authenticates a user.
     * The example implementation makes sure if the username and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * @return boolean whether authentication succeeds.
     */
    public function authenticate()
    {
        $criteria = new CDbCriteria;
        if (isset($this->id)) {
            $criteria->condition = 'id=:id';
            $criteria->params['id'] = $this->id;
        } else {
            $criteria->condition = 'username=:username';
            $criteria->params['username'] = $this->username;
        }
        $record = User::model()->find($criteria);
        if ($record === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif ($record->password !== $record->hashPassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        }
        //elseif ($record->email_confirmed != null) {
          //  $this->errorCode = self::ERROR_EMAIL_INVALID;
        //}
        else {
            $this->user = $record;
            $this->errorCode = self::ERROR_NONE;
        }
        return !$this->errorCode;
    }

    public function getId(){
        return $this->user->id;
    }

    public function getName() {
        return $this->user->username;
    }

}
<?php
/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class WebgameUserIdentity extends UserIdentity
{
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
        $criteria->condition = 'username=:username';
        $criteria->params['username'] = $this->username;
        $record = User::model()->find($criteria);
        $this->user = $record;
    }
    public function getId(){
        return $this->user->id;
    }

    public function getName() {
        return $this->user->username;
    }
}
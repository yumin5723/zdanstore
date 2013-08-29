<?php

class AdminIdentity extends CUserIdentity {
    protected $_id;
    /**
     * function_description
     *
     *
     * @return
     */
    public function authenticate() {
        $manager = Manager::model()->findByAttributes(
            array('username'=>$this->username)
        );
        if ($manager == null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif ($manager->password !== $manager->hashPassword($this->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->_id = $manager->id;
            $manager->last_login_time = date("Y-m-d H:i:s");
            $manager->save(false);
            $this->errorCode = self::ERROR_NONE;
        }

        return !$this->errorCode;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getId() {
        return $this->_id;
    }
    /**
     * function_description
     *
     *
     * @return
     */
    public function getName() {
        return $this->username;
    }
}
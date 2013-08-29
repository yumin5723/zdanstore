<?php
require_once("GameAbstract.php");
class Doudizhu extends GameAbstract{
    /**
     * function_description
     *
     *
     * @return
     */
    public function __construct($account,$nickname,$password,$gender="",$avatar="") {

        // $this->init();
        $this->account = $account;
        $this->nickname = $nickname;
        $this->password = $password;
        $this->gender = $gender;
        $this->avatar = $avatar;
    }
}
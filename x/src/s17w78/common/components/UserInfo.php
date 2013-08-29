<?php
class UserInfo{
	protected $_uid;
	protected $_user;
	protected $_profile;

	public static function factory($uid){
		$info = new self;
		if(is_numeric($uid)){
			$info->_uid = $uid;
		}elseif($uid instanceof User){
			$info->_uid = $uid->id;
			$info->_user = $uid;
		}
		return $info;
	}
	public function getUser(){
		if(is_null($this->_user)){
			$this->_user = User::model()->findByPk($this->_uid);
		}
		return $this->_user;
	}
	public function getProfile(){
		if(is_null($this->_profile)){
			$this->_profile = Profile::model()->findByPk($this->_uid);
			if(empty($this->_profile)){
				$this->_profile = new Profile;
				$this->_profile->uid = $this->_uid;
			}
		}
		return $this->_profile;
	}

	public function __get($name){
		return $this->getUser()->$name;
	}

	public function __isset($name){
		return isset($this->_user->$name);
	}

	public function __unset($name){
		$this->_user->__unset($name);
	}

	public function __call($name, $parameters) {
        if (method_exists($this->getProfile(), $name)) {
            return call_user_func_array(array($this->getProfile(), $name), $parameters);
        }
        if (method_exists($this->getUser(), $name)) {
            return call_user_func_array(array($this->getUser(),$name), $parameters);
        }
        throw new CException(Yii::t('yii','{class} does not have a method named "{name}".',array('{class}'=>get_class($this), '{name}'=>$name)));
    }
    /*
     *  return username
     */
    function getUsername() {
          $username = empty($this->getUser()->username) ?  $this->getUser()->email : $this->getUser()->username;
          return $username;
    }
    /*
     *  return email
     */
    function getUserEmail() {
        if ($email = $this->getUser()->email) {
            return $email;
        }
    }
    /*
     *  return nickname
     */
    function getNickname(){
    	$nickname = $this->getUser()->nickname;
    	return $nickname;
    }
    /*
     *  return nickname
     */
    function getUserisreg(){
        $status = $this->getUser()->is_reg;
        if($status == User::IS_NOT_REG){
            return false;
        }
        return true;
    }
    /*
     *  return avatar
     */
    function getAvatar(){
    	if(!preg_match('/^(http:\/\/\S*)$/', $this->getProfile()->small_avatar)){
            $avatar = empty($this->getProfile()->small_avatar) ? "" : Yii::app()->params['avatar_url'].$this->getProfile()->small_avatar;
        }else{
            $avatar = $this->getProfile()->small_avatar;
        }
        return $avatar;
    }
    /**
     * get user gold
     * @return [type] [description]
     */
    function getGold(){
    	$gold = UserGoldTotal::model()->findByPk($this->getUser()->id);
    	if(empty($gold)){
    		return 0;
    	}
    	return intval($gold->gold);
    }
    /**
     * get user played game recently
     * @return array
     */
    function getPlayedgames(){
        return UserPlayed::model()->getUserPlayedGamesRecently($this->getUser()->id);
    }
}
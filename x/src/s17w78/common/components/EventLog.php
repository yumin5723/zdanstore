<?php
class EventLog extends CApplicationComponent {

    /**
     * send event 
     * @param  string $action       user touch action
     * @param  string $ip           user client ip address
     * @param  strint $url          current url
     * @param  int  $uid            user id ,if user is guest null
     * @param  strint $referrer     url referrer
     * @param  current_time         if current time is null the time is time
     * @return boolean
     */
    public function send($action,$ip,$url,$uid = null,$referrer = null){
        $result = ActionLog::model()->saveActionLog($action,$ip,$url,$uid,$referrer);
        if($result === false){
            Yii::log("this action is send fail". $action, 'error');
        }
    }
}
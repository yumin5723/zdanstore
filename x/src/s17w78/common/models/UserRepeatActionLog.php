<?php

class UserRepeatActionLog {
    private static $_prefix_today_fetch_code = "probablity:fetch_code_tody:";
    private static $_ip_hour_fetch_code = "probablity:ip_fetch_code_hour:";
    private static $_model = null;
    protected $_readIns = null;
    protected $_writeIns = null;

    /**
     * function_description
     *
     *
     * @return
     */
    public static function model() {
        if (self::$_model == null) {
            self::$_model = new self;
        }
        return self::$_model;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getRead() {
        if ($this->_readIns == null) {
            $this->_readIns = Yii::app()->TaoHaoRedis->getReadIns();
        }
        return $this->_readIns;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getWrite() {
        if ($this->_writeIns == null) {
            $this->_writeIns = Yii::app()->TaoHaoRedis->getWriteIns();
        }
        return $this->_writeIns;
    }

    /**
     *
     *
     * @param package_id
     * @param uid
     *
     * @return
     */
    public function getUserTodayFetchCodeTimes($package_id,$uid) {
        $r = $this->getRead();
        return intval($r->get($this->getKeyForUserTodayFetchCodeTimes($package_id,$uid)));
    }

    /**
     * update user today fetch code times
     *
     * @param $package_id:
     * @param $uid:
     *
     * @return
     */
    public function updateUserTodayFetchCodeTimes($package_id,$uid) {
        $r = $this->getWrite();
        $r->incr($this->getKeyForUserTodayFetchCodeTimes($package_id,$uid));
        return True;
    }

    function getKeyForUserTodayFetchCodeTimes($package_id,$uid) {
        $day = date("Y-m-d");
        return sprintf("%s%s:%s:%s", self::$_prefix_today_fetch_code, $day, $package_id, $uid);
    }
    /**
     *
     *
     * @param ip
     *
     * @return
     */
    public function getIpHourFetchCodeTimes($ip) {
        $r = $this->getRead();
        return intval($r->get($this->getKeyForIpHourFetchCodeTimes($ip)));
    }
    /**
     * update ip per hour fetch code times
     *
     * @param $ip:
     *
     * @return
     */
    public function updateIpHourFetchCodeTimes($ip) {
        $r = $this->getWrite();
        $r->incr($this->getKeyForIpHourFetchCodeTimes($ip));
        return True;
    }

    function getKeyForIpHourFetchCodeTimes($ip) {
        $hour = date("Y-m-d H");
        return sprintf("%s%s:%s", self::$_ip_hour_fetch_code, $hour,$ip);
    }
}

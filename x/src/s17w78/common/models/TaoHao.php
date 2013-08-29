<?php
class TaoHao {
    private static $_prefix = "tao:";
    private static $_model = null;
    protected $_readIns=null;
    protected $_writeIns=null;

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
     * random get
     *
     * @param $package_id:
     * @param $num:
     *
     * @return array( array1("code", count), ....)
     */
    public function getAllByPackageId($package_id,$num=10) {
        try {
            $r = $this->getRead();
            $key = $this->getKey($package_id);
            $r->multi(Redis::PIPELINE);
            for ($i=0; $i<2*$num; $i++) {
                $r = $r->sRandMember($key);
            }
            $ret = $r->exec();
            $codes = array_slice(array_diff(array_unique($ret), array(false)), 0, $num);

            //update for code tao count
            $r = $this->getWrite();
            $r->multi(Redis::PIPELINE);
            foreach ($codes as $code) {
                $k = $this->getCodeKey($package_id, $code);
                $r = $r->incr($k);
            }
            $counts = $r->exec();
            return array_map(null, $codes, $counts);
        } catch (Exception $e) {
            Yii::log(sprintf("Error in fetch all Tao code from redis, with Gameid: %d, Num: %d, E_MSG: %s", $package_id, $num, $e->getMessage()), CLogger::LEVEL_ERROR, 'application.models.TaoHao');
            return array();
        }
    }
    /**
     * function_description
     *
     * @param $package_id:
     * @param array $codes:
     * @todo: wait for phpredis/sadd support multiple memebers
     * @return boolen
     */
    public function updateByPackageId($package_id) {
        $codes = ActiveCode::model()->getAllCanTaoCodesArray($package_id);
        // savet to temp set
        /**
         * from redis 2.4, sadd all multiple members.
         * but phpredis extension is not support now.
         */
        $r = $this->getWrite();
        $r->multi(Redis::PIPELINE);
        $temp_key = $this->get_temp_key($package_id);
        foreach ($codes as $c) {
            $r = $r->sAdd($temp_key, $c);
        }
        $r->exec();

        //move/replace to real key
        $r->rename($temp_key, $this->getKey($package_id));
        return true;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function get_temp_key($package_id) {
        return sprintf("%s%s:%s", self::$_prefix, $package_id,":temp");
    }

    /**
     * get code count key
     *
     * @param $package_id:
     * @param $code:
     *
     * @return
     */
    public function getCodeKey($package_id, $code) {
        return sprintf("%s%s:%s", self::$_prefix, $package_id, $code);
    }

    /**
     * function_description
     *
     * @param $package_id:
     *
     * @return
     */
    protected function getKey($package_id) {
        return sprintf("%s%s:%s",self::$_prefix, $package_id, "all");
    }

}
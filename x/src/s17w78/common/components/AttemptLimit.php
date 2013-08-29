<?php

class AttemptLimit extends CApplicationComponent {
    /**
     * @var integer how many times should the same CAPTCHA be displayed. Defaults to 3.
     * A value less than or equal to 0 means the test is unlimited (available since version 1.1.2).
     */
    public $testLimit = 1;

    public $max_attempt = 5;

    public $prefix="i1378:login_limit:";

    public $input_name = "captcha";

    public $caseSensitive = false;

    /**
     * The name of the GET parameter indicating whether the CAPTCHA image should be regenerated.
     */
    const REFRESH_GET_VAR='refresh';
    /**
     * Prefix to the session variable name used by the action.
     */
    const SESSION_VAR_PREFIX='Yii.CCaptchaAction.';

     /**
     * @var integer the minimum length for randomly generated word. Defaults to 6.
     */
    public $minLength = 6;
    /**
     * @var integer the maximum length for randomly generated word. Defaults to 7.
     */
    public $maxLength = 7;
    /**
     * function_description
     *
     *
     * @return
     */
    public function init() {
        parent::init();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function getKey() {
        return $this->prefix.Yii::app()->request->getUserHostAddress();
    }


    /**
     * function_description
     *
     *
     * @return
     */
    public function attempt_fail() {
        $w = Yii::app()->redis->getWriteIns();
        $w->incr($this->getKey());
        $w->expire($this->getKey(), 3600);
    }

    /**
     * function_description
     *
     *
     * @return boolean
     */
    public function need_captcha() {
        $r = Yii::app()->redis->getReadIns();
        return $r->get($this->getKey()) >= $this->max_attempt;
    }

    /**
     * Returns the session variable name used to store verification code.
     * @return string the session variable name
     */
    protected function getSessionKey()
    {
        return self::SESSION_VAR_PREFIX . Yii::app()->getId() . ".attemptlimit";
    }

    /**
     * Validates the input to see if it matches the generated code.
     * @param string $input user input
     * @param boolean $caseSensitive whether the comparison should be case-sensitive
     * @return [boolean, message], boolean whether the input is valid
     */
    public function validate()
    {
        if (!$this->need_captcha()) {
            return array(true,null);
        }
        if(empty($_REQUEST[$this->input_name])){
            return array(false,"请输入验证码");
        }
        $msg = "";
        $input = $_REQUEST[$this->input_name];
        $code = $this->getVerifyCode();
        $valid = $this->caseSensitive ? ($input === $code) : !strcasecmp($input,$code);
        if (!$valid) {
            $msg="验证码错误";
        }
        $session = Yii::app()->session;
        $session->open();
        $name = $this->getSessionKey() . 'count';
        $session[$name] = $session[$name] + 1;
        if($session[$name] > $this->testLimit && $this->testLimit > 0)
            $this->getVerifyCode(true);
        return array($valid,$msg);
    }

    /**
     * Gets the verification code.
     * @param boolean $regenerate whether the verification code should be regenerated.
     * @return string the verification code.
     */
    public function getVerifyCode($regenerate=false)
    {
        $session = Yii::app()->session;
        $session->open();
        $name = $this->getSessionKey();
        if($session[$name] === null || $regenerate)
        {
            $session[$name] = $this->generateVerifyCode();
            $session[$name . 'count'] = 1;
        }
        return $session[$name];
    }

    /**
     * Generates a new verification code.
     * @return string the generated verification code
     */
    protected function generateVerifyCode()
    {
        if($this->minLength < 3)
            $this->minLength = 3;
        if($this->maxLength > 20)
            $this->maxLength = 20;
        if($this->minLength > $this->maxLength)
            $this->maxLength = $this->minLength;
        $length = mt_rand($this->minLength,$this->maxLength);

        $letters = 'bcdfghjklmnpqrstvwxyz';
        $vowels = 'aeiou';
        $code = '';
        for($i = 0; $i < $length; ++$i)
        {
            if($i % 2 && mt_rand(0,10) > 2 || !($i % 2) && mt_rand(0,10) > 9)
                $code.=$vowels[mt_rand(0,4)];
            else
                $code.=$letters[mt_rand(0,20)];
        }

        return $code;
    }


}

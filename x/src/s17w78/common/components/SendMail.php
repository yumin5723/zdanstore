<?php

class SendMail extends CApplicationComponent {
    public $config_file = null;

    protected $_db_file = null;
    protected $_db = null;
    protected $_send_command = null;

    /**
     * function_description
     *
     *
     * @return
     */
    protected function getDbConnection() {
        if ($this->_db_file == null) {
            $config = parse_ini_file($this->config_file);
            $this->_db_file = $config['db_file'];
        }

        if ($this->_db == null) {
            $dsn = "sqlite:" . $this->_db_file;
            $this->_db = new CDbConnection($dsn);
            $this->_db->active = true;
        }

        return $this->_db;
    }

    /**
     * get command
     *
     *
     * @return
     */
    protected function getSendCommand() {
        if ($this->_send_command == null) {
            $sql = "INSERT INTO mail (`to_send`, `subject`, `content`, `mail_type`, `status`, `created`) values (:to_send, :subject, :content, :mail_type, 0, :created)";

            $this->_send_command = $this->getDbConnection()->createCommand($sql);
        }

        return $this->_send_command;
    }


    /**
     *
     *
     * @param $to_send:
     * @param $subject:
     * @param $message:
     *
     * @return
     */
    public function send($to_send, $subject, $content, $type) {
        $command = $this->getSendCommand();
        $command->bindParam(":to_send", $to_send, PDO::PARAM_STR);
        $command->bindParam(":subject", $subject, PDO::PARAM_STR);
        $command->bindParam(":content", $content, PDO::PARAM_STR);
        $command->bindParam(":mail_type", $type, PDO::PARAM_STR);
        $time = date("Y-m-d H:i:s");
        $command->bindParam(":created", $time);
        $command->execute();
        return True;
    }

}
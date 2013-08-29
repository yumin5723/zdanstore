<?php
Yii::import("common.models.UserAccounts");
abstract class GameAbstract {
    public $account = "";
    public $nickname = "";
    public $avatar = "";
    public $gender = "";
    public $password = "";
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

    public function createAccount(){
        // $model = new UserAccounts;
        // $user = UserAccounts::model()->findByAttributes(array('Accounts'=>$this->account));
        // if(empty($user)){
        //     $model->Accounts = $this->account;
        //     $model->RegAccounts = $this->nickname;
        //     $model->LogonPass = md5($this->password);
        //     $model->InsurePass = md5($this->password);
        //     $model->RegisterDate = date('Y-m-d',time());
        //     $model->save(false);
        // }else{
        //     if($user->RegAccounts != $this->nickname){
        //         $user->RegAccounts = $this->nickname;
        //         $user->save(false);
        //     }
        // }
        // return true;
        include_once(Yii::getPathOfAlias("common.config")."/sqlconnect.php");
        $sql = "select * from UserAccounts where Accounts = '{$this->account}'";
        $query = mssql_query($sql);
        $row = mssql_fetch_assoc($query);
        $password = md5($this->password);
        $date = date('Y-m-d',time());
        $gender = isset($this->gender) ? $this->gender : 0;
        if(empty($row)){
            //create user
            $sql = "insert into UserAccounts(Accounts,RegAccounts,Gender,LogonNullity,ServiceNullity,LogonPass,InsurePass,RegisterDate,MemberDate) values('{$this->account}','{$this->nickname}','{$gender}',0,2,'{$password}','{$password}','{$date}','{$date}')";
            $query = mssql_query($sql);
            if($query){
                return true;
            }
        }else{
            if($row['RegAccounts'] != $this->nickname){
                $sql = "update UserAccounts set RegAccounts = '{$this->nickname}' where Accounts = '{$this->account}'";
                $query = mssql_query($sql);
                if($query){
                    return true;
                }
            }
        }
    }
}
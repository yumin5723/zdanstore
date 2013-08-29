<?php
class  RegDataCommand extends CConsoleCommand {

    // protected $reg_date;/

    protected $reg_date;
    // protected $channel;
    protected $channel = '131test';
    protected $offset = 0;

    protected $max = 100;

    public $data = array();

    public $content = "";

    public $title = "";

    protected $end_date;
    /**
     * function_description
     *
     *
     * @return
     */
    public function run($args) {
        if(isset($args[2])){
            list($this->channel,$this->reg_date,$this->end_date) = $args;
            // $startdate = strtotime($this->reg_date);
            // $enddate = strtotime($this->end_date);
            // $days=round(($enddate-$startdate)/3600/24);
            // for($i=0;$i<=$days;$i++){
            //     $t = date('Y-m-d',strtotime("+{$i} day",strtotime($this->reg_date)));
            //     $this->offset = 0;
            //     $this->getUserRegDataByDate($t);
            // }
            $this->getUserRegDataByDate($this->reg_date);
            $this->getUserRegDataByDate($this->end_date);
        }else{
            list($this->channel,$this->reg_date) = $args;
            $this->getUserRegDataByDate($this->reg_date);
        }
        foreach($this->data as $r_date=>$data){
            $this->title .= $r_date.",".$data['user_sum'].",";
            foreach($data['re'] as $k=>$v){
                if($k == $r_date){
                    if($v == 0){
                        $rate = 0;
                    }else{
                        $rate = $data['re'][$r_date];
                    }
                }else{
                    if($v == 0){
                        $rate = 0;
                    }else{
                        $rate = sprintf("%.2f",$v/$data['re'][$r_date]*100)."%";
                    }
                }
                $this->title .= $rate.",";
            }
            $this->title .= "\n";
        }
        echo $this->title;
    }

    /**
     * get tasks from database
     *
     * @param $num:
     *
     * @return
     */
    public function getRegUsers($channel,$reg_date,$offset,$num = 100) {
        return ActionLog::model()->getRegUsersByDate($channel,$reg_date,$offset,$num);
    }
    /**
     * get user reg date by date
     * @return [type] [description]
     */
    public function getUserRegDataByDate($request_date){
        $this->data[$request_date]['user_sum'] = 0;
        $this->data[$request_date]['uids'] = array();
        while (true) {
            $users = $this->getRegUsers($this->channel,$request_date,$this->offset);
            $this->data[$request_date]['user_sum'] += count($users);
            foreach ($users as $user) {
                $this->data[$request_date]['uids'][] = $user->uid;
            }
            if (empty($users)) {
                Yii::app()->db->setActive(false);
                break;
            }
            $this->offset += $this->max;
        }
        $startdate = strtotime($request_date);
        $enddate = time();
        $days=round(($enddate-$startdate)/3600/24);
        if($days > 7){
            $days = 7;
        }
        for($i=0;$i<$days;$i++){
            $t = date('Y-m-d',strtotime("+{$i} day",strtotime($request_date)));
            $this->data[$request_date]['re'][$t] = 0;
            $count = 30;
            $j = 0;
            while (true) {
                $uids = array_slice($this->data[$request_date]['uids'], $j,$count);
                $replay_users = UserPlayLog::model()->getPlayUsersByDate(date('Y-m-d',strtotime(" +{$i} day",strtotime($request_date))),$uids);
                $this->data[$request_date]['re'][$t] += count($replay_users);
                $j += $count;
                if($j >= count($this->data[$request_date]['uids'])){
                    break;
                }
            }
        }
        // $this->title .= "\n";
        // $this->content .= "\n";
        // fwrite($handel,$this->title);
        // fwrite($handel,$this->content);
        // fclose($handel);
    }
}

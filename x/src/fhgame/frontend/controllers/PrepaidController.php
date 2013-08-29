<?php
Yii::import("application.components.payment.*");
class PrepaidController extends CController {
    public $paychannel = array(
        'alipay'=>'Alipay',
        'dianxin'=>'Dianxin',
        'kuaiqian'=>'Kuaiqian',
        'liantong'=>'Liantong',
        'shenzhouxing'=>'Shenzhouxing',
    );
    /**
     * @return array action filters
     */
    public function filters() {
        
        return array(
            'accessControl', // perform access control for CRUD operations
            
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        
        return array(
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'index',
                    'pay',
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }
    
    public function actionIndex(){
        $this->render("list");
    }

    public function actionPay(){


        $type = $_GET['type'];
        if (isset($this->paychannel[$type])) {
            if (isset($_POST['amount'])) {
                
                $payment = new $this->paychannel[$type]($_POST['amount']);
                $result = $payment->output();
                $this->render("pay_next",array('result'=>$result,'type'=>$type));
            } else {
                $this->render("pay_pay_1",array('type'=>$type));
            }
        }
        
    }
}

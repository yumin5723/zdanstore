<?php
Yii::app()->getComponent("payment");
// Yii::import("common.extensions.spay.Payment");
Yii::import("common.extensions.spay.channels.card.ShenZhouFu");
// Yii::import("common.extensions.spay.models.*");
// Yii::import('common.extensions.spay.PayParam');
// Yii::import('common.extensions.spay.ChargeParam');
class TestShenZhouFu extends CDbTestCase {
        public $_notify_data = array();

    public $fixtures = array(
    );

    /**
     * function_description
     *
     *
     * @return
     */
    public function setUp() {
        parent::setUp();
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function testGetOuterData() {


        $pay_data['uid'] = "102";
        $pay_data['cp_id'] = "102";
        $pay_data['app_id'] = "1012";
        $pay_data['game_account'] = "username";
        $pay_data['amount'] = "30";
        $pay_data['card_type'] = "szf_unicom";
        $pay_data['card_no'] = "123456789";
        $pay_data['card_pwd'] = "987654321";

        // $order = Yii::app()->payment->directPay($pay_data);
        $order = Order::model()->findByPk("256");
        $this->assertNotEmpty($order);

        $pay_method = "szf_unicom";
        list($channel,$err) = Yii::app()->payment->getChannelByMethod($pay_method, $order);
        $this->assertNotEmpty($channel);
        // var_dump($channel);exit;
        $pay = $order->createNewPay($channel->channel_name,$pay_method);
        $this->assertNotEmpty($channel);
        $channel->addCustomMsg("pay_id", $pay->id);
        $outerdata = $channel->getOuterData();
        list($url,$req_data) = $outerdata;

        $this->assertNotEmpty($url);
        $this->assertInternalType("array",$req_data);
        

        $channel = new ShenZhouFu;
        $outerdata = $channel->getOuterData();
        $this->assertNotEmpty($outerdata);
        $this->assertInternalType('object', $outerdata);
        $order = Order::model()->findByPk("256");
        $this->assertNotEmpty($order);


        $pay_method = "szf_unicom";
        list($channel,$err) = Yii::app()->payment->getChannelByMethod($pay_method, $order=null);
        $this->assertNotEmpty($channel);
        // var_dump($channel);exit;
        // $pay = $order->createNewPay($channel->channel_name,$pay_method);
        $channel->addCustomMsg("pay_id", $pay->id);
        $outerdata = $channel->getOuterData();

        $this->assertNotEmpty($outerdata);
        $this->assertInternalType('object', $outerdata);



        $order = new Order;
        $pay_method = "szf_unicom";
        list($channel,$err) = Yii::app()->payment->getChannelByMethod($pay_method, $order);
        $this->assertNotEmpty($channel);
        $channel->addCustomMsg("pay_id", $pay->id);
        $outerdata = $channel->getOuterData();
        
        $this->assertNotEmpty($outerdata);
        $this->assertInternalType('object', $outerdata);
    }

    public function testCheckResponse(){
        $channel = new ShenZhouFu;
        $data = "200";
        $ret = $channel->checkResponse($data);
        $this->assertTrue($ret);

        $channel = new ShenZhouFu;
        $data = "201";
        $ret = $channel->checkResponse($data);
        $this->assertFalse($ret);
    }


    public function testValidNotify(){
        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "0";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ..";
        $data['payDetails'] = "";

        // $data['md5String'] = "asdfadfadfasdf";

        $channel->setNotify($data);

        $ret = $channel->validNotify();
        $this->assertFalse($ret);


        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "0";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";

        $data['md5String'] = "asdfadfadfasdf";

        $channel->setNotify($data);

        $ret = $channel->validNotify();
        $this->assertFalse($ret);


        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "0";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";

        $data['md5String'] = "21e477dd1d227ef16a3220959df1807e";

        $channel->setNotify($data);

        $ret = $channel->validNotify();
        $this->assertTrue($ret);

    }

    public function testIsNotifySuccess(){
        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "0";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";

        $data['md5String'] = "21e477dd1d227ef16a3220959df1807e";

        $channel->setNotify($data);
        $ret = $channel->isNotifySuccess();

        $this->assertFalse($ret);

        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "1";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";

        $data['md5String'] = "0c9fae2e4f8d589f85f39529d2468c87";

        $channel->setNotify($data);
        $ret = $channel->isNotifySuccess();

        $this->assertTrue($ret);
    }


    public function testGetMethodIdFromNotify(){
        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "1";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "555";

        $data['md5String'] = "0c9fae2e4f8d589f85f39529d2468c87";

        $channel->setNotify($data);
        $ret = $channel->getMethodIdFromNotify();

        $this->assertNotEmpty($ret);
        $this->assertInternalType("string",$ret);
        $this->assertEquals('szf_unicom',$ret);

    }

    public function testGetReturnStringForNotify(){
        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "1";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";

        $data['md5String'] = "0c9fae2e4f8d589f85f39529d2468c87";

        $channel->setNotify($data);
        $ret = $channel->getReturnStringForNotify();

        $this->assertNotEmpty($ret);
        $this->assertInternalType("string",$ret);
        $this->assertEquals('success',$ret);


        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "1";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "dd";

        $data['md5String'] = "0c9fae2e4f8d589f85f39529d2468c87";

        $channel->setNotify($data);
        $ret = $channel->getReturnStringForNotify();

        $this->assertNotEmpty($ret);
        $this->assertInternalType("string",$ret);
        $this->assertEquals('false',$ret);

    }

    public function testGetPayAmount(){
        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "1";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";

        $data['md5String'] = "0c9fae2e4f8d589f85f39529d2468c87";

        $channel->setNotify($data);
        $ret = $channel->getPayAmount();
        $this->assertNotEmpty($ret);
        $this->assertEquals('3000',$ret);
    }

    public function testSetCustomMsgFromNotify(){
        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "1";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";

        $data['md5String'] = "0c9fae2e4f8d589f85f39529d2468c87";

        $channel->setNotify($data);

        $ret = $channel->setCustomMsgFromNotify();
        $this->assertInternalType("object",$ret);
    } 

    public function testGetReturnMsg() {
        $ret_data = "101";

        $channel = new Shenzhoufu;

        $ret = $channel->getReturnMsg($ret_data);
        $this->assertInternalType('string',$ret);


        $ret_data = "";

        $channel = new Shenzhoufu;

        $ret = $channel->getReturnMsg($ret_data);
        $this->assertEmpty($ret);
    }

    public function testGetNotifyMsg(){
        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "1";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";
        $data['errcode'] = "200";

        $data['md5String'] = "0c9fae2e4f8d589f85f39529d2468c87";

        $channel->setNotify($data);

        $ret = $channel->getNotifyMsg();
        $this->assertInternalType("string",$ret);
        $this->assertEquals("充值卡验证成功",$ret);


        $channel = new Shenzhoufu;
        $data['version'] = "3";
        $data['merId'] = "151525";
        $data['payMoney'] = "3000";
        $data['orderId'] = "20130731-151525-274";
        $data['cardMoney'] = "50";
        $data['payResult'] = "1";
        $data['privateField'] = "eyJwYXlfaWQiOiIxMDcifQ";
        $data['payDetails'] = "";
        $data['errcode'] = "";

        $data['md5String'] = "0c9fae2e4f8d589f85f39529d2468c87";

        $channel->setNotify($data);

        $ret = $channel->getNotifyMsg();
        $this->assertEmpty($ret);

    }
}
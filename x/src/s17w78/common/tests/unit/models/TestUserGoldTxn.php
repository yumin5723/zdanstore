<?php

Yii::import("common.models.UserGoldTotalTxn");
class TestUserGoldTxn extends CDbTestCase {
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
    public function testSaveTransaction() {

        $uid = 55;
        $gold = -4;
        $tid = -1;
        $good_id = 1420;
        $info = "goods info";
        $consume = true;        

        $r = UserGoldTxn::model()->saveTransaction($uid,$gold,$tid,$good_id,$info,$consume);

        $this->assertEquals(1,$r);

        $uid = 55;
        $gold = 400;
        $tid = -1;
        $good_id = 1420;
        $info = "goods info";
        $consume = true;        

        $r = UserGoldTxn::model()->saveTransaction($uid,$gold,$tid,$good_id,$info,$consume);

        $this->assertEquals(3,$r);

        $uid = 55;
        $gold = -4;
        $tid = -1;
        $good_id = 1420;
        $info = "goods info";
        $consume = true;        

        $r = UserGoldTxn::model()->saveTransaction($uid,$gold,$tid,$good_id,$info,$consume);

        $this->assertEquals(1,$r);
    }
}
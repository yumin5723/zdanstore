<?php

Yii::import("common.models.UserGold");
class TestUserPlayLog extends CDbTestCase {
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
    public function testGetUserTotalGold() {
        $uid = 55;
        $r = UserGold::model()->getUserTotalGold($uid);
        $this->assertEquals(82,$r);

        $uid = 552314234;
        $r = UserGold::model()->getUserTotalGold($uid);
        $this->assertEquals(0,$r);


    }

    public function testSpendGold(){
        $uid = 55;
        $gold = -4;
        $tid = -1;
        $game_id = 20;
        $info = "this is a unit test";

        $r = UserGold::model()->spendGold($uid,$gold,$tid,$game_id,$info);
        $this->assertEquals(1,$r);

        $gold = 5000;
        $r = UserGold::model()->spendGold($uid,$gold,$tid,$game_id,$info);
        $this->assertEquals(0,$r);

        $uid = 0;
        $gold = 50;
        $r = UserGold::model()->spendGold($uid,$gold,$tid,$game_id,$info);
        $this->assertEquals(0,$r);

        $uid = 55;
        $gold = 50;
        $r = UserGold::model()->spendGold($uid,$gold,$tid,$game_id,$info);
        $this->assertEquals(1,$r);

    }

    public function testIncomeGold(){
        $uid = 55;
        $gold = 40;
        $tid = -1;
        $game_id = 20;
        $info = "this is a unit test";

        $gold = 40;
        $r = UserGold::model()->incomeGold($uid,$gold,$tid,$game_id,$info);
        $this->assertEquals($r, 1);

        $gold = 0;
        $r = UserGold::model()->incomeGold($uid,$gold,$tid,$game_id,$info);
        $this->assertEquals($r, 1);
    }

}
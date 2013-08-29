<?php

Yii::import("common.models.UserGoldTotal");
class TestUserGoldTotal extends CDbTestCase {
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
    public function testGetUserGold() {
        $uid = 55;
        $r = UserGoldTotal::model()->getUserGold($uid);
        $this->assertEquals($r, 40);
        $this->assertEquals($r, 0.00);

        $uid = 0;
        $this->assertEquals($r, 240.00);

        $uid = 0;
        $this->assertEquals($r, 0.00);
    }
}
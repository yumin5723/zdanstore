<?php

Yii::import("common.models.UserPlayTime");
class TestUserPlayTime extends CDbTestCase {
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
    public function testUpdateAllTime() {
        $uid = 1;
        $app_id = 1001;
        $t = 100;

        $r1 = UserPlayTime::model()->updateAllTime($uid, $app_id, $t);
        $this->assertTrue($r1);
        $m = UserPlayTime::model()->findByAttributes(array('uid'=>$uid,'app_id'=>$app_id));
        $this->assertEquals($t, $m->all_time);

        $r2 = UserPlayTime::model()->updateAllTime($uid, $app_id, $t);
        $this->assertTrue($r2);
        $m = UserPlayTime::model()->findByAttributes(array('uid'=>$uid,'app_id'=>$app_id));
        $this->assertEquals(2*$t, $m->all_time);
    }


}

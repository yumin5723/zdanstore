<?php

Yii::import("common.models.UserPlayTime");
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
    public function testFlushUserOnline() {
        $uid = 1;
        $app_id = 1001;

        // make new online status
        $start_time = time();
        $r = UserPlayLog::model()->flushUserOnline($uid, $app_id, $start_time);
        $this->assertTrue($r);
        $m = UserPlayLog::model()->findByAttributes(array(
                 'uid'=>$uid,
                 'app_id'=>$app_id,
                 'start_time' => date('Y-m-d H:i:s', $start_time),
             ));
        $this->assertNotEmpty($m);
        $this->assertEquals($m->start_time, $m->end_time);

        // update online time
        $addtime = rand(0, UserPlayLog::MAX_IDLE - 1);
        $current_time = $start_time +  $addtime;
        $r = UserPlayLog::model()->flushUserOnline($uid, $app_id, $current_time);
        $m = UserPlayLog::model()->findByAttributes(array(
                 'uid'=>$uid,
                 'app_id'=>$app_id,
                 'start_time' => date('Y-m-d H:i:s', $start_time),
             ));
        $this->assertNotEmpty($m);
        $this->assertEquals(date('Y-m-d H:i:s', $current_time), $m->end_time);
        $all_time = UserPlayTime::model()->findByAttributes(array(
                 'uid'=>$uid,
                 'app_id'=>$app_id,
                    ))->all_time;
        $this->assertGreaterThan(0, $all_time);

        // update again
        $addtime = rand(0, UserPlayLog::MAX_IDLE - 1);
        $current_time = $current_time +  $addtime;
        $r = UserPlayLog::model()->flushUserOnline($uid, $app_id, $current_time);
        $m = UserPlayLog::model()->findByAttributes(array(
                 'uid'=>$uid,
                 'app_id'=>$app_id,
                 'start_time' => date('Y-m-d H:i:s', $start_time),
             ));
        $this->assertNotEmpty($m);
        $this->assertEquals(date('Y-m-d H:i:s', $current_time), $m->end_time);
        $new_all_time = UserPlayTime::model()->findByAttributes(array(
                 'uid'=>$uid,
                 'app_id'=>$app_id,
                    ))->all_time;
        $this->assertEquals($addtime, $new_all_time - $all_time);

        // test new record
        $new_start_time = $current_time + UserPlayLog::MAX_IDLE + 1;
        $r = UserPlayLog::model()->flushUserOnline($uid, $app_id, $new_start_time);
        $this->assertTrue($r);
        $ms = UserPlayLog::model()->findAllByAttributes(array(
                 'uid'=>$uid,
                 'app_id'=>$app_id,
              ));
        $this->assertGreaterThan(1, count($ms));
   }


}
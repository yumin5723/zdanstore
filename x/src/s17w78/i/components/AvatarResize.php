<?php
class AvatarResize extends ImageFile {
    const USER_AVATAR_FOLDER = 'avatar';
    const AVATAR_SIZE = 100;
    const FILE_MAX_SIZE = 4096000;
    const NEW_IMAGE_SIZE = 180;
    protected $_uid = null;
    protected $_pos = null;

    public function __construct($origin_file, $uid,$post) {
        parent::__construct($origin_file);
        $this->_uid = $uid;
        $this->_pos = $post;
    }

    /**
     * function_description
     *
     * @param $full_path:
     *
     * @return
     */
    protected function make_thumb($full_path) {
        //check image
        Yii::import('gcommon.lib.wideimage.WideImage');
        $image = WideImage::load($this->_origin);
        $resized = $image->resize($this->_pos['width'], $this->_pos['height'],'outside'); 

        $white = $image->allocateColor(255, 255, 255);
        $croped = $resized->resizeCanvas(self::NEW_IMAGE_SIZE, self::NEW_IMAGE_SIZE, $this->_pos['left'], $this->_pos['top'],$white);
        $croped->saveToFile($full_path);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function _save_file() {
        $file_path = $this->generate_filename();
        $full_path = $this->_base_path.'/'.$file_path;
        $this->check_dir($full_path);

        //save file thumb
        $this->make_thumb($full_path);
        //update to database
        $userInfo = Profile::model()->findByPk($this->_uid);
        $old_file = $this->_base_path.'/'.$userInfo->getSmallAvatarUri();
        $userInfo->update_smallavatar($file_path);
        @unlink($old_file);

        $this->_uri = $file_path;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function generate_filename() {
        return self::USER_AVATAR_FOLDER.'/'.$this->random_folder().'/'.$this->_uid.'_'.mt_rand(0, 65535).'.'.$this->get_extension_name();
    }

}
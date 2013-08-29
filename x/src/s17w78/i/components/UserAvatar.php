<?php
class Avatar_Exception extends CException {}

class UserAvatar extends ImageFile {
    const USER_AVATAR_FOLDER = 'avatar';
    const AVATAR_SIZE = 100;
    const FILE_MAX_SIZE = 5242880;
    protected $_uid = null;

    public function __construct($origin_file, $uid) {
        parent::__construct($origin_file);
        $this->_uid = $uid;
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
        //filesize
        if (filesize($this->_origin) > self::FILE_MAX_SIZE) {
            throw new Adminimage_Exception('upload size limit to 4M.');
        }

        Yii::import('gcommon.lib.wideimage.WideImage');
        $image = WideImage::load($this->_origin);
        $size = 100;
//        if ($image->getWidth() < $size || $image->getHeight() < $size) {
//            throw new Adminimage_Exception("upload image too small.");
//        }

        //resize image
        $resized = $image->resize($image->getWidth(), $image->getHeight(), 'outside');
        //crop to
        $cropX = 0;
        $cropY = 0;
//        if ($resized->getWidth() > $size) {
//            $cropX = intval(($resized->getWidth() - $size) / 2);
//        }
//        if ($resized->getHeight() > $size) {
//            $cropY = intval(($resized->getHeight() - $size) /2);
//        }
        $croped = $resized->crop($cropX, $cropY, $resized->getWidth(), $resized->getHeight());
        $croped->saveToFile($full_path);
        return true;
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
        $userInfo = Profile::model()->findByPk($this->_uid);
        $old_file = $this->_base_path.'/'.$userInfo->getAvatarUri();
        $userInfo->update_avatar($file_path);
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
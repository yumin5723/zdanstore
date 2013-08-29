<?php

class ImageFile {
    protected $_origin = null;
    protected $_saved = false;
    protected $_base_path = null;
    protected $_uri = null;
    protected $_thumb_uri = null;

    protected static $_mime_to_ext = array(
        'image/bmp'  =>'bmp',
        'image/gif'  =>'gif',
        'image/ief'  =>'ief',
        'image/jpeg' =>'jpg',
        'image/png'  =>'png',
        'image/tiff' =>'tiff',
    );

    /**
     * function_description
     *
     * @param $origin_file:
     *
     * @return
     */
    public function __construct($origin_file) {
        $this->_origin = $origin_file;
        $this->_base_path = Yii::app()->params['user_image_path'];
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function get_file_uri() {
        if (!$this->_saved) {
            $this->_save_file();
        }
        return $this->_uri;
    }
        /**
     * function_description
     *
     *
     * @return
     */
    public function get_thumb_uri() {
        if (!$this->_saved) {
            $this->_save_file();
        }

        return $this->_thumb_uri;
    }
    /**
     * function_description
     *
     * @param $file_path:
     *
     * @return
     */
    protected function check_dir($full_path) {
        $dir_path = dirname($full_path);
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0777, true);
        }
        return true;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function _save_file() {
        $file_path = $this->generate_filename();
        $full_path = $this->_base_path.'/'.$file_path;
        $this->check_dir($full_path);

        //mv file
        copy($this->_origin, $full_path);
        $this->_uri = $file_path;
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function random_folder() {
        return sprintf('%02x/%02x', mt_rand(0,255), mt_rand(0, 255));
    }

    /**
     * function_description
     *
     *
     * @return
     */
    protected function get_extension_name() {
        if ($ext = CFileHelper::getExtension($this->_origin)) {
            return $ext;
        }

        $mimetype = CFileHelper::getMimeType($this->_origin);
        if ($mimetype && isset(self::$_mime_to_ext[$mimetype])) {
            return self::$_mime_to_ext[$mimetype];
        }
        return 'ukn';
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function generate_filename() {

    }

}
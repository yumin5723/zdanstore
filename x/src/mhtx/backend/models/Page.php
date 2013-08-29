<?php

class Page extends CActiveRecord {
    /**
     * page draft
     */
    const STATUS_DRAFT = 1;
    /**
     * page already published
     */
    const STATUS_PUBLISHED = 2;
    
    const STATUS_NEED_PUBLISH = 3;
    
    const ERROR_ATTRIBUTE_VALUE = 3001;

    /**
     * function_description
     *
     * @param $className:
     *
     * @return
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function tableName() {
        return 'page';
    }

    /**
     * function_description
     *
     *
     * @return
     */
    public function rules() {
        return array(
            array('page_name,domain,path','required'),
            array('file', 'file','allowEmpty'=>true,'types'=>'rar','maxSize'=>1024 * 1024 * 4,'tooLarge'=>'The file was larger than 4MB. Please upload a smaller file.'),
        );
    }

    /**
     * function_description
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
        );
    }
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
              'CTimestampBehavior' => array(
                    'class'               => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute'     => 'created',
                    'updateAttribute'     => 'modified',
                    'timestampExpression' => 'date("Y-m-d H:i:s")',
                    'setUpdateOnCreate'   => true,
                ),
           )
        );
    }
    /**
     * save new Rar file when create page
     *
     *
     * @return array(boolean result, MError err)
     * result for if new file have saved success.
     * err is the error for not created or saved. if saved success, the err is null
     */
    public function saveRarFile($files){
        $tmp_file = $files['Page']['tmp_name']['file'];
        $real_file = $files['Page']['name']['file'];
        $file = new UploadFile($tmp_file,$real_file);
        $uri = $file->get_file_uri();
        if($uri == ""){
            return false; 
        }
        return $uri;
    }
}
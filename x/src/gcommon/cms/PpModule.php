<?php
/**
 * CmsModule.php --- Cms module class file
 *
 * @Author: Sleepdragon
 * @Maintainer:
 * @Copyright: Copyright &copy; 2010-2011 Moofa.com
 */



class PpModule extends CWebModule {
    private $_assetsUrl;
    public $domain;
    /**
     * Initializes the cms module.
     */
    public function init() {
        parent::init();
        $this->setImport(array(
                'cms.models.*',
                'cms.components.*',
                'gcommon.assets.*',
            ));
    }


	/**
	 * @return string the base URL that contains all published asset files of gii.
	 */
	public function getAssetsUrl()
	{
		if($this->_assetsUrl===null)
			$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('gcommon.assets'));
			$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('cms.assets'));
		return $this->_assetsUrl;
	}

	/**
	 * @param string $value the base URL that contains all published asset files of gii.
	 */
	public function setAssetsUrl($value)
	{
		$this->_assetsUrl=$value;
	}

}

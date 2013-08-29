<?php
Yii::import("gcommon.components.GController");
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BackendController extends GController
{
    protected $_assetsUrl;

    /**
    * Returns the URL to the published assets folder.
    * @return string the URL
    */
    protected function getAssetsUrl()
    {
        if (isset($this->_assetsUrl))
            return $this->_assetsUrl;
        else
        {
            $assetsPath = Yii::getPathOfAlias('application.assets');
            $assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, YII_DEBUG);
            return $this->_assetsUrl = $assetsUrl;
        }
    }


}
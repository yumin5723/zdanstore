<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class WController extends CController
{

	public $layout = false;

	protected $_assetsUrl;
    /**
     * function_description
     *
     * @param $viewName:
     *
     * @return
     */
    public function getViewFile($viewName) {
        $viewName = $this->getId()."/".$viewName.Yii::app()->getViewRenderer()->fileExtension;
        if (($module=$this->getModule())!==null) {
            $viewName = "@".$module->getId().'/'.$viewName;
        }
        return $viewName;
    }

    public function getBaseView() {
        $module = $this->getModule();
        if (isset($module->baseView)) {
            return $module->baseView;
        } else {
            return "layouts/base".Yii::app()->getViewRenderer()->fileExtension;
        }
    }

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
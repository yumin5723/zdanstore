<?php
// /**
//  * CmsModule.php --- Cms module class file
//  *
//  * @Author: Sleepdragon
//  * @Maintainer:
//  * @Copyright: Copyright &copy; 2010-2011 Moofa.com
//  */



// class PpModule extends CWebModule {
//     private $_assetsUrl;
//     public $domain;
//     /**
//      * Initializes the cms module.
//      */
//     public function init() {
//         parent::init();
//         $this->setComponents(array(
//                 'errorHandler' => array(
//                         'errorAction' => 'pp/site/error'),
//                 'user' => array(
//                         'class' => 'CWebUser',
//                         'loginUrl' => Yii::app()->createUrl('pp/site/login'),
//                 	)
//                 )
//         );
//         Yii::app()->user->setStateKeyPrefix('_zdanstore_admin');
//         $this->setImport(array(
//                 'cms.models.*',
//                 'cms.components.*',
//                 'gcommon.assets.*',
//             ));
//     }


// 	/**
// 	 * @return string the base URL that contains all published asset files of gii.
// 	 */
// 	public function getAssetsUrl()
// 	{
// 		if($this->_assetsUrl===null)
// 			$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('gcommon.assets'));
// 			$this->_assetsUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('cms.assets'));
// 		return $this->_assetsUrl;
// 	}

// 	/**
// 	 * @param string $value the base URL that contains all published asset files of gii.
// 	 */
// 	public function setAssetsUrl($value)
// 	{
// 		$this->_assetsUrl=$value;
// 	}

// }

class PpModule extends CWebModule {
    public $assetsUrl;
    public $defaultController = 'site';
    public $domain;
    public function init() {

        // this method is called when the module is being created
        $this->setComponents(array(
                'errorHandler' => array(
                        'errorAction' => 'pp/login/error'),
                'user' => array(
                        'class' => 'CWebUser',
                        'loginUrl' => Yii::app()->createUrl('/pp/site/login'),
                        'identityCookie'=>array('domain'=>'www.zdanstore-test.com'),
                        'returnUrl'=> array('/pp/site/index'),
                )
                )
        );

        // Yii::app()->user->setStateKeyPrefix('_pp_zdanstore_admin');

        // import the module-level models and components
        $this->setImport(array(
                'cms.models.*',
                'cms.components.*',
                'gcommon.assets.*'
                )
        );
    }

    public function beforeControllerAction($controller, $action) {

        if(parent::beforeControllerAction($controller, $action)) {
            // this method is called before any module controller 
            //action is performed
            $route = $controller->id . '/' . $action->id;


            $publicPages = array(
                    'site/login',
                    'site/error',
            );

            if (Yii::app()->user->name !== 'admin' && !in_array($route, 
                              $publicPages)) {
                Yii::app()->getModule('pp')->user->loginRequired();

            } else {
                return true;
            }
        }
        else
            return false;
    }
}

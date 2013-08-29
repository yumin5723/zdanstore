<?php
class MatrixcardController extends CController {
	/**
     * @return array action filters
     */
    public function filters() {
        
        return array(
            'accessControl', // perform access control for CRUD operations
            
        );
    }
    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        
        return array(
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'index',
                    'creatematrixcard',
                ) ,
                'users' => array(
                    '*'
                ) ,
            ) ,
        );
    }

    public function actionIndex(){
    	$this->render('user_mibaoka0');
    }

    public function actionBindcard(){
    	$model = new Retakepwd;
        if (isset($_POST['Retakepwd'])) {
            $model->attributes = $_POST['Retakepwd'];
            if ($model->validate()) {
            	$matrixcard = new Matrixcard;
        		$result = $matrixcard->getquesbindcard($model->username);
        		if ($result[0] == false) {
                    $this->redirect('/matrixcard/success/type/3');
        		} else {
	                $userQues = $result[1];
	                $this->render('user_mibaoka2',array('model'=>$model,'username'=>$model->username,'userQues'=>$userQues,'id_num'=>$model->id_num));
	                return ;
        		}
            } 
        } 
        $this->render("user_mibaoka1",array('model'=>$model));
    }

    public function actionVerifybind(){
        $model = new Retakepwd;
        if (isset($_POST['Retakepwd'])) {
            $model->setScenario('verifyanswer');
            $model->attributes = $_POST['Retakepwd'];
            if ($model->validate()) {
        		$matrixcard = new Matrixcard;
        		$result = $matrixcard->bindcard($model->username);
        		if ($result[0] === false) {
        			$this->redirect('/matrixcard/success/type/3');
        		} elseif ($result[0] === 1) {
        			$this->render('user_mibaoka4',array('username'=>$model->username,'id_num'=>$model->id_num));
        			return;
        		} else {
        			$gtime = time();
					$key = md5($gtime."mhtxmatrixcard");
					Yii::app()->SESSION['username'] = $model->username;
        			$this->render('user_mibaoka3',array('gtime'=>$gtime,'key'=>$key));
        			return;
        		}
            }
            $verify = new Retakepwd;
            $userQues = $verify->getUserQues($model->username);
            $this->render('user_mibaoka2',array('model'=>$model,'username'=>$model->username,'userQues'=>$userQues,'id_num'=>$model->id_num));

        }else{
            $this->redirect('index');
        }
        
    }

    public function actionRebind(){
        $model = new Retakepwd;
        if (isset($_POST['Retakepwd'])) {
            $model->attributes = $_POST['Retakepwd'];
            if ($model->validate()) {
            	$matrixcard = new Matrixcard;
            	$result = $matrixcard->rebindcard($model->username);
            	if ($result > 0) {
            		$gtime = time();
					$key = md5($gtime."mhtxmatrixcard");
        			$this->render('user_mibaoka3',array('gtime'=>$gtime,'key'=>$key));
        			return;
            	}
            } 
        } 

    }

    public function actionUnbindcard(){
    	$model = new Retakepwd;
        if (isset($_POST['Retakepwd'])) {
            $model->attributes = $_POST['Retakepwd'];

            if ($model->validate()) {

                $verify = new Retakepwd;
                $userQues = $verify->getUserQues($model->username);
                $this->render('un_user_mibaoka2',array('model'=>$model,'username'=>$model->username,'userQues'=>$userQues,'id_num'=>$model->id_num));
                return ;
            } 
        } 
        $this->render("un_user_mibaoka1",array('model'=>$model));
    }

    public function actionVerifyunbind(){
        $model = new Retakepwd;
        if (isset($_POST['Retakepwd'])) {
            $model->setScenario('verifyanswer');
            $model->attributes = $_POST['Retakepwd'];
            if ($model->validate()) {
        		$matrixcard = new Matrixcard;
        		$str = $matrixcard->unbindcard($model->username);
                if ($str == "1") {
                    $this->redirect('/matrixcard/success/type/1');
                } else if ($str == "2") {
                    $this->redirect('/matrixcard/success/type/2');
                }
                $this->redirect('/matrixcard/success/type/0');
            }
            $verify = new Retakepwd;
            $userQues = $verify->getUserQues($model->username);
            $this->render('un_user_mibaoka2',array('model'=>$model,'username'=>$model->username,'userQues'=>$userQues,'id_num'=>$model->id_num));

        }else{
            $this->redirect('index');
        }
        
    }


    public function actionSuccess(){
        $type = $_GET['type'];
        $this->render('succ',array('type'=>$type));
    }

    public function actionCreatematrixCard(){
    	if (isset(Yii::app()->SESSION['username']) && Yii::app()->SESSION['username'] != "") {
    		if ($_GET['gtime'] < (time()-300) || $_GET['gtime'] > (time()+3) || $_GET['key'] != md5($_GET['gtime'].'mhtxmatrixcard'))      {
                return $this->redirect('/matrixcard/success/type/4');
    		}
    		$model = new Matrixcard;
    		$pic = $model->getMatricard(Yii::app()->SESSION['username']);



    		header("Cache-Control: no-cache, must-revalidate");
			header("Content-type:image/jpeg;charset:utf8");
			$filename = "密保卡".date("Y-m-dHis").".jpg";
			$encoded_filename = urlencode($filename);
			$encoded_filename = str_replace("+", "%20", $encoded_filename);
			$ua = $_SERVER["HTTP_USER_AGENT"];

			if (preg_match("/MSIE/", $ua)) {
			header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
			} else if (preg_match("/Firefox/", $ua)) {
			header('Content-Disposition: attachment; filename*="utf8\'\'' . $filename . '"');
			} else {
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			}


    		imagejpeg($pic);  //在浏览器中输出图片
	        imagedestroy($simage); //结束图片，释放内存
	        imagedestroy($pic);
    	}
        $this->redirect('fail');
    }
}
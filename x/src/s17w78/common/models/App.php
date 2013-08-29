<?php

class App extends CActiveRecord
{
    const GAME_IS_ONLINE = 0;
    const GAME_IS_TEST = 1;
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'app';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
            array('app_name,short_name,gate_chn,charge_chn,type,status','required'),
			array('charge_key,gate_key,gate_id,gate_url,charge_url,tel,charge_amount','safe'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=&gt;label)
	 */
	public function attributeLabels()
	{
		return array(
			'app_name'=>'游戏名称',
            'short_name'=>'游戏简称',
			'tel'=>'客服电话',
            'charge_amount'=>'充值金额',
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
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;
        $criteria->order = "id DESC";
        $criteria->compare('id',$this->id,true);
        $criteria->compare('app_name',$this->app_name,true);
        $criteria->compare('short_name',$this->short_name,true);
        $criteria->compare('type',$this->type,true);
        $criteria->compare('created',$this->created,true);
        $criteria->compare('modified',$this->modified,true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'=>$criteria,
            'pagination' => array(
                    'pageSize' => 20,
                )
        ));
    }

    /**
     * function_description
     *
     * @param $attributes:
     *
     * @return
     */
    public function updateAttrs($attributes) {
        $attrs = array();
        if (!empty($attributes['app_name']) && $attributes['app_name'] != $this->app_name) {
            $attrs[] = 'app_name';
            $this->app_name = $attributes['app_name'];
        }
        if (!empty($attributes['short_name']) && $attributes['short_name'] != $this->short_name) {
            $attrs[] = 'short_name';
            $this->short_name = $attributes['short_name'];
        }
        if (!empty($attributes['charge_chn']) && $attributes['charge_chn'] != $this->charge_chn) {
            $attrs[] = 'charge_chn';
            $this->charge_chn = $attributes['charge_chn'];
        }
        if (!empty($attributes['gate_chn']) && $attributes['gate_chn'] != $this->gate_chn) {
            $attrs[] = 'gate_chn';
            $this->gate_chn = $attributes['gate_chn'];
        }
        if (!empty($attributes['charge_key']) && $attributes['charge_key'] != $this->charge_key) {
            $attrs[] = 'charge_key';
            $this->charge_key = $attributes['charge_key'];
        }
        if (!empty($attributes['gate_key']) && $attributes['gate_key'] != $this->gate_key) {
            $attrs[] = 'gate_key';
            $this->gate_key = $attributes['gate_key'];
        }
        if (!empty($attributes['gate_id']) && $attributes['gate_id'] != $this->gate_id) {
            $attrs[] = 'gate_id';
            $this->gate_id = $attributes['gate_id'];
        }
         if (!empty($attributes['gate_url']) && $attributes['gate_url'] != $this->gate_url) {
            $attrs[] = 'gate_url';
            $this->gate_url = $attributes['gate_url'];
        }
        if (!empty($attributes['charge_url']) && $attributes['charge_url'] != $this->charge_url) {
            $attrs[] = 'charge_url';
            $this->charge_url = $attributes['charge_url'];
        }
        if (!empty($attributes['type']) && $attributes['type'] != $this->type) {
            $attrs[] = 'type';
            $this->type = $attributes['type'];
        }
        if ($attributes['status'] != $this->status) {
            $attrs[] = 'status';
            $this->status = $attributes['status'];
        }
        if (!empty($attributes['charge_amount']) && $attributes['charge_amount'] != $this->charge_amount) {
            $attrs[] = 'charge_amount';
            $this->charge_amount = $attributes['charge_amount'];
        }
        if (!empty($attributes['tel']) && $attributes['tel'] != $this->tel) {
            $attrs[] = 'tel';
            $this->tel = $attributes['tel'];
        }
        if ($this->validate($attrs)) {
            return $this->save(false);
        } else {
            return false;
        }
    }

    public function getgatechntypes(){
        $global = $this->getglobal();
        $a = array_keys($global['gates']);
        $a1 = $a;
        $b = array_combine($a, $a1);
        return $b;
    }

    public function getchargechntypes(){
        $global = $this->getglobal();
        $a = array_keys($global['charges']);
        $a1 = $a;
        $b = array_combine($a, $a1);
        return $b;
    }
    private function getglobal(){
        return require_with_local(Yii::getPathOfAlias("common.config.cooperation")."/global.php");
    }
   /**
     * get cpuser key by user id
     *
     * @param int $cpuser_id: cpuser's id
     *
     * @return str
     */
    public function getChargeKeyById($app_id){
        $id = intval($app_id);
        $result = self::model()->findByPk($id);
        
        if(empty($result)){
            return "";
        }
        return $result->charge_key;
        
    }
    /**
     * get gate key app
     *
     * @param int app id
     *
     * @return str
     */
    public function getGateKeyById($app_id){
        $id = intval($app_id);
        $result = self::model()->findByPk($id);
        
        if(empty($result)){
            return "";
        }
        return $result->gate_key;
        
    }
    /**
     * get gate url 
     *
     * @param int app id
     *
     * @return str
     */
    public function getGateUrlById($app_id){
        $id = intval($app_id);
        $result = self::model()->findByPk($id);
        
        if(empty($result)){
            return "";
        }
        return $result->gate_url;
        
    }
    /**
     * get gate id 
     *
     * @param int app id
     *
     * @return str
     */
    public function getGateIdById($app_id){
        $id = intval($app_id);
        $result = self::model()->findByPk($id);
        
        if(empty($result)){
            return "";
        }
        return $result->gate_id;
        
    }
    public function getAppname($app_id){
        $id = intval($app_id);
        $result = self::model()->findByPk($id);
        return $result->app_name;
    }
    /**
     * get game type
     * @return [type] [description]
     */
    public function getAppType(){
        $results = WebgameTerm::model()->findAll();
        $ret = array();
        foreach ($results as $key=> $value) {
            $ret[$value->id] = $value->name;
        }
        return $ret;
    }
    /**
     * get game status
     */
    public function getStatus(){
        return array("0"=>'正式上线','1'=>'测试中');
    }
    /**
     * check user is in test user
     * @return [type] [description]
     */
    public function checkUserAuth($uid){
        $result = TestUser::model()->findByPk($uid);
        if(empty($result)){
            return false;
        }
        return true;
    }
    /**
     * get app by type if type is 0 return all apps online
     * @param  integer $type [description]
     * @return [type]        [description]
     */
    public function getAppByType($type = 0){
        $criteria=new CDbCriteria;
        if($type == 0){
            $criteria->condition = 'status=:status';
            $criteria->params = array(":status"=>self::GAME_IS_ONLINE);
        }else{
            $criteria->condition = 'status=:status AND type = :type';
            $criteria->params = array(":status"=>self::GAME_IS_ONLINE,":type"=>$type);
        }
        return self::model()->findAll($criteria);
    }
}
<?php
Yii::import("gcommon.cms.models.ObjectTerm");
/**
 * This is the model class for table "{{object_term}}".
 *
 * The followings are the available columns in table '{{object_term}}':
 * @property string $object_id
 * @property string $term_id
 * @property integer $term_order
 */
class FhObjectTerm extends ObjectTerm
{
	/**
	 * save terms for object
	 * @param  intval $page_id 
	 * @param  array $terms 
	 * @return boolean
	 */
	public function saveObjectTerm($object_id,$terms){
		foreach ( $terms as $term ) {
			if (!self::model()->findByPk(array("object_id"=>$object_id,"term_id"=>$term))){
				$obj_term=new self;
	            $obj_term->object_id=$object_id;
	            $obj_term->term_id=$term;
	            $obj_term->save(false);
			}
		}
	}
	/**
	 * update terms for object
	 * @param  intval $object_id 
	 * @param  array $terms 
	 * @return boolean
	 */
	public function updateObjectTerm($object_id,$terms){
		// get current dependence
        $current = $this->getTermsByObject($object_id);
        // calculate need to delete
        $to_del = array_diff($current, $terms);
        // calculate need to insert
        $to_insert = array_diff($terms, $current);

        // save to db
        if (!empty($to_del)) {
            $this->removeTerms($object_id,$to_del);
        }
        if (!empty($to_insert)) {
            $this->addTerms($object_id,$to_insert);
        }
        return true;
	}
	/**
     * get terms of page id 
     *
     * @param $page_id:
     *
     * @return
     */
    protected function getTermsByObject($object_id) {
        $rows = $this->getDbConnection()->createCommand()
                     ->select('term_id')
                     ->from($this->tableName())
                     ->where(array('and',
                             'object_id=:object_id',
                         ),
                         array(
                             ':object_id'=>$object_id,
                         ))
                     ->queryAll();
        return array_map(function($a){return $a['term_id'];},$rows);
    }
    /**
     * function_description
     *
     *
     * @return
     */
    protected function addTerms($model_id,$termids) {
        if (empty($termids)) {
            return true;
        }
        $sql = "INSERT INTO " . $this->tableName() . " (object_id,term_id) VALUES (:object_id,:term_id)";
        $cmd = $this->getDbConnection()->createCommand($sql);
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd->bindParam(":object_id", $model_id);
        foreach ($termids as $id) {
            if(empty($id)){
                continue;
            }
            $cmd->bindParam(":term_id", $id);
            $cmd->execute();
        }
        return true;
    }


    /**
     * function_description
     *
     * @param $model_type:
     * @param $model_id:
     * @param $dep_type:
     * @param $dep_ids:
     *
     * @return
     */
    protected function removeTerms($model_id,$termids) {
        if (empty($termids)) {
            return true;
        }
        $sql="DELETE FROM " . $this->tableName() .
        " WHERE object_id=:object_id ";
        $sql .= " AND term_id in ('".implode("','",$termids)."')";
        if (!is_array($termids)) {
            $termids = array($termids);
        }
        $cmd = $this->getDbConnection()->createCommand($sql);
        $cmd->bindParam(":object_id", $model_id);
        return $cmd->execute();
    }
    /**
     * save terms for object
     * @param  intval $page_id 
     * @param  array $terms 
     * @return boolean
     */
    public function saveGameTerm($object_id,$term){
        if (!self::model()->findByPk(array("object_id"=>$object_id,"term_id"=>$term))){
            $obj_term=new self;
            $obj_term->object_id=$object_id;
            $obj_term->term_id=$term;
            $obj_term->save(false);
        }
    }
    /**
     * update terms for object
     * @param  intval $object_id 
     * @param  array $terms 
     * @return boolean
     */
    public function updateGameTerm($object_id,$term){
        if(!is_array($term)){
            $terms[] = $term;
        }
        // get current dependence
        $current = $this->getTermsByObject($object_id);
        // calculate need to delete
        $to_del = array_diff($current, $terms);
        // calculate need to insert
        $to_insert = array_diff($terms, $current);

        // save to db
        if (!empty($to_del)) {
            $this->removeTerms($object_id,$to_del);
        }
        if (!empty($to_insert)) {
            $this->addTerms($object_id,$to_insert);
        }
        return true;
    }
}
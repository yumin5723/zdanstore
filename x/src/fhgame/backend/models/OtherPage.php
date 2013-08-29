<?php
Yii::import("gcommon.cms.models.Page");
class OtherPage extends Page
{
     /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search() {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria=new CDbCriteria;

        $ids = array_merge(PageTerm::model()->getIdsBelongsToActivity(),array("1","2","3","4"));
        // list page id not in 1,2,3,4 and not in activity ids

        $criteria->condition = "";
        $criteria->addNotInCondition("id",$ids);

        $sort = new CSort;
        $sort->attributes = array(
            'id',
        );
        $sort->defaultOrder = 'id DESC';


        return new CActiveDataProvider( $this, array(
                'criteria'=>$criteria,
                'pagination' => array(
                    'pageSize' => 20,
                ),
                'sort'=>$sort
            ) );
    }
}
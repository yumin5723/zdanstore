<?php

class PackageRecommend extends CActiveRecord
{
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
		return 'package_recommend';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
            'package' => array(self::BELONGS_TO, 'Package', 'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=&gt;label)
	 */
	public function attributeLabels()
	{
		return array(
		);
	}

	public function getRightRecommend(){
		$bigRecommend = self::model()->findByPk("1");
		$smallValue = self::model()->findByPk("2")->value;
		$recommendArr = explode(",", $smallValue);
		$smallRecommend = Package::model()->getRecommend($recommendArr);
		return array($bigRecommend,$smallRecommend);
	}

}
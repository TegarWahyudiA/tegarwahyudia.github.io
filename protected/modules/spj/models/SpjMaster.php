<?php

class SpjMaster extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'm_spj_dokumen';
	}

	public function rules()
	{
		return array(
			array('dokumen', 'required'),
			array('dokumen', 'length', 'max'=>32),
			array('dokumen', 'safe', 'on'=>'search'),
		);
	}
	public function relations()
	{
		return array(
			'spj'=>array(self::HAS_MANY, 'SpjDokumen','id_dokumen'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'dokumen' => 'Nama Dokumen',
		);
	}

	public function search()
	{

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('dokumen',$this->dokumen, true);
		$criteria->order = "dokumen";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

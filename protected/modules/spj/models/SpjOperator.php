<?php

class SpjOperator extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'm_spj_operator';
	}

	public function rules()
	{
		return array(
			array('id_pegawai', 'required'),
			array('id_pegawai', 'numerical', 'integerOnly'=>true),
			array('id_pegawai', 'safe', 'on'=>'search'),
		);
	}
	public function relations()
	{
		return array(
			'pegawai'=>array(self::BELONGS_TO, 'MasterPegawai','id_pegawai'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_pegawai' => 'Nama Pegawai',
		);
	}

	public function search()
	{

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_pegawai',$this->id_pegawai);
		$criteria->order = "pegawai.nama_pegawai";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

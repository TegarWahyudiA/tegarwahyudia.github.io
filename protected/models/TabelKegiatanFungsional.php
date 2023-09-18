<?php

class TabelKegiatanFungsional extends CActiveRecord
{

	public function tableName()
	{
		return 't_kegiatan_fungsional';
	}


	public function rules()
	{
		return array(
			array('id_fungsional, id_kegiatan, kode_kredit', 'required'),
			array('id_fungsional, id_kegiatan, kode_kredit', 'numerical', 'integerOnly'=>true),
			array('faktor_pengali','numerical'),

			array('id, id_fungsional, id_kegiatan, kode_kredit', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'kegiatan'=>array(self::BELONGS_TO,'TabelKegiatan','id_kegiatan'),
			'fungsional'=>array(self::BELONGS_TO,'MasterFungsional','id_fungsional'),
//			'kredit'=>array(self::BELONGS_TO,'MasterKredit','id_kredit'),
		);
	}

	public function getKredit()
	{
		return MasterKredit::model()->findByAttributes(array(
			'kode_tingkat'=>$this->id_fungsional,
			'kode'=>$this->kode_kredit,
		));
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_kegiatan' => 'Kegiatan',
			'id_fungsional' => 'Jabatan Fungsional',
			'kode_kredit' => 'Angka Kredit',
			'faktor_pengali' => 'Faktor Pengali',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('id_kegiatan',$this->id_kegiatan);
		$criteria->compare('id_fungsional',$this->id_fungsional);
		$criteria->compare('kode_kredit',$this->kode_kredit);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

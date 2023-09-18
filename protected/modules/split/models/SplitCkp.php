<?php
class SplitCkp extends CActiveRecord
{
	public function getDbConnection()
	{
		return Yii::app()->split_db;
	} 

	public function tableName()
	{
		return 'kk_ckp';
	}

	public function rules()
	{
		return array(
			array('tahun, bulan, id_wilayah, id_pegawai, jml_kegiatan, r_kuantitas, r_kualitas, nilai_ckp', 'required'),
			array('tahun, bulan, id_wilayah, id_pegawai, id_unitkerja, id_penilai, jml_kegiatan', 'numerical', 'integerOnly'=>true),
			array('r_kuantitas, r_kualitas, nilai_ckp, angka_kredit', 'numerical'),
			array('keterangan', 'length', 'max'=>128),
			array('id, tahun, bulan, id_wilayah, id_pegawai', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'wilayah'=>array(self::BELONGS_TO,'MasterWilayah','id_wilayah'),
			'unitkerja'=>array(self::BELONGS_TO,'MasterUnitkerja','id_unitkerja'),
			'pegawai'=>array(self::BELONGS_TO,'SplitPegawai','id_pegawai'),
			'penilai'=>array(self::BELONGS_TO,'SplitPegawai','id_penilai'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_wilayah' => 'Wilayah',
			'id_pegawai' => 'Pegawai',
			'keterangan' => 'Keterangan',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_wilayah',$this->id_wilayah);
		$criteria->compare('id_pegawai',$this->id_pegawai);

		$criteria->order='id_wilayah, pegawai.nama_pegawai';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}

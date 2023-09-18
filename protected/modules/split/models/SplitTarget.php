<?php
class SplitTarget extends CActiveRecord
{
	public function getDbConnection()
	{
		return Yii::app()->split_db;
	} 

	public function tableName()
	{
		return 'kk_target';
	}

	public function rules()
	{
		return array(
			array('id_wilayah, id_kegiatan, id_pegawai, jml_target', 'required'),
			array('id_wilayah, id_kegiatan, id_pegawai, jml_target, jml_realisasi, persen_kualitas', 'numerical', 'integerOnly'=>true),
			array('keterangan', 'length', 'max'=>128),
			array('id, id_wilayah, id_kegiatan, id_pegawai', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'wilayah'=>array(self::BELONGS_TO,'MasterWilayah','id_wilayah'),
			'kegiatan'=>array(self::BELONGS_TO,'SplitKegiatan','id_kegiatan'),
			'pegawai'=>array(self::BELONGS_TO,'SplitPegawai','id_pegawai'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_wilayah' => 'Wilayah',
			'id_pegawai' => 'Pegawai',
			'id_kegiatan' => 'Kegiatan',
			'jml_target' => 'Target',
			'jml_realisasi' => 'Realisasi',
			'persen_kualitas' => 'Kualitas',
			'keterangan' => 'Keterangan',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_wilayah',$this->id_wilayah);
		$criteria->compare('id_pegawai',$this->id_pegawai);
		$criteria->compare('id_kegiatan',$this->id_kegiatan);

		$criteria->order='id_wilayah, kegiatan.nama_kegiatan, pegawai.nama_pegawai';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}

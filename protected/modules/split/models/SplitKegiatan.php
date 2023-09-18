<?php
class SplitKegiatan extends CActiveRecord
{
	public function getDbConnection()
	{
		return Yii::app()->split_db;
	} 

	public function tableName()
	{
		return 'kk_kegiatan';
	}

	public function rules()
	{
		return array(
			array('tahun, bulan, id_wilayah, id_kegiatan, id_unitkerja, nama_kegiatan, satuan, jml_target, tgl_mulai, tgl_selesai', 'required'),
			array('tahun, bulan, id_wilayah, id_kegiatan, id_unitkerja, jml_target, is_ckp, kode_terampil, kode_ahli', 'numerical', 'integerOnly'=>true),
			array('nama_kegiatan, keterangan', 'length', 'max'=>128),
			array('satuan', 'length', 'max'=>32),
			array('tgl_mulai, tgl_selesai', 'length', 'min'=>10, 'max'=>10),
			array('id, tahun, bulan, id_wilayah, id_unitkerja, nama_kegiatan', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'wilayah'=>array(self::BELONGS_TO,'MasterWilayah','id_wilayah'),
			'unitkerja'=>array(self::BELONGS_TO,'MasterUnitkerja','id_unitkerja'),
//			'terampil'=>array(self::HAS_ONE,'MasterKredit','','on'=>'kode=kode_terampil AND kode_tingkat=1'),
//			'ahli'=>array(self::HAS_ONE,'MasterKredit','','on'=>'kode=kode_terampil AND kode_tingkat=2'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tahun' => 'Tahun',
			'bulan' => 'Bulan',
			'id_wilayah' => 'Wilayah',
			'id_unitkerja' => 'Unitkerja',
			'nama_kegiatan' => 'Nama Kegiatan',
			'satuan' => 'Satuan',
			'jml_target' => 'Target',
			'tgl_mulai' => 'Mulai',
			'tgl_selesai' => 'Deadline',
			'is_ckp' => 'Tampil di CKP',
			'kode_terampil' => 'Fungsional Terampil',
			'kode_ahli' => 'Fungsional Ahli',
			'keterangan' => 'Keterangan',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('tahun',$this->tahun);
		$criteria->compare('bulan',$this->bulan);
		$criteria->compare('id_wilayah',$this->id_wilayah);
		$criteria->compare('id_unitkerja',$this->id_unitkerja);
		$criteria->compare('nama_kegiatan',$this->nama_kegiatan,true);

		$criteria->order='id_wilayah, tahun, bulan, nama_kegiatan';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}

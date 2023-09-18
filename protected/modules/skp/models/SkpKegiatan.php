<?php

class SkpKegiatan extends CActiveRecord
{
	public function tableName()
	{
		return 't_skp_kegiatan';
	}

	public function rules()
	{
		return array(
			array('id_unitkerja, tahun, nama_kegiatan, id_satuan, jml_bulan', 'required'),
			array('id_unitkerja, tahun, id_satuan, jml_target, id_flag, id_pegawai_usulan, jml_bulan', 'numerical', 'integerOnly'=>true),
			array('jml_bulan', 'numerical', 'min'=>1, 'max'=>12),
			array('nama_kegiatan', 'length', 'max'=>128),
			array('id_unitkerja, tahun, keterangan', 'safe'),

			array('id, id_flag, nama_kegiatan, id_unitkerja, id_satuan, jml_target, keterangan, id_pegawai_usulan, created_on', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'unitkerja'=>array(self::BELONGS_TO,'MasterUnitkerja','id_unitkerja'),			
			'satuan'=>array(self::BELONGS_TO,'MasterSatuan','id_satuan'),
			'pegawai'=>array(self::HAS_MANY,'SkpPegawai','id_kegiatan'),	
			'pegawai_usulan'=>array(self::BELONGS_TO,'MasterPegawai','id_pegawai_usulan'),	
		);
	}

	public function getTarget_satuan()
	{
		return $this->jml_target.' '.$this->satuan->nama_satuan;
	}

	public function getChild_target()
	{
		return Yii::app()->db->createCommand("SELECT SUM(jml_target) FROM ".SkpPegawai::model()->tableName()." WHERE id_kegiatan=".$this->id)->queryScalar();
	}

	public function getJml_realisasi()
	{
		return Yii::app()->db->createCommand("SELECT SUM(jml_realisasi) FROM ".SkpRealisasi::model()->tableName()." WHERE id_kegiatan=".$this->id)->queryScalar();
	}

	public function getProgress()
	{
		return $this->jml_target? $this->jml_realisasi / $this->jml_target * 100 : 0;
	}
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nama_kegiatan' => 'Nama Kegiatan',
			'id_unitkerja' => 'Unitkerja',
			'tahun' => 'Tahun',
			'id_satuan' => 'Satuan',
			'jml_target' => 'Jml Target',
			'jml_bulan' => 'Jml Bulan',
			'keterangan' => 'Keterangan',
			'created_on' => 'Created On',
			'target_satuan' => 'Target',
			'child_target' => 'Dialokasikan',
			'id_flag' =>'Usulan Kegiatan',
			'id_pegawai_usulan'=>'Yang Mengusulkan',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('nama_kegiatan',$this->nama_kegiatan,true);
		$criteria->compare('id_unitkerja',$this->id_unitkerja);
		$criteria->compare('id_satuan',$this->id_satuan);
		$criteria->compare('jml_target',$this->jml_target);
		$criteria->compare('keterangan',$this->keterangan,true);
		$criteria->compare('created_on',$this->created_on,true);

		$criteria->order='nama_kegiatan ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function beforeSave()
	{
	   if(parent::beforeSave())
	   {
	        $this->nama_kegiatan = strtoupper(substr($this->nama_kegiatan, 0,1)).substr($this->nama_kegiatan, 1);
	        return true;
	   }
   		return false;
	}
}

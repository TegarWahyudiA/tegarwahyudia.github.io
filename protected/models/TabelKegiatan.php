<?php

class TabelKegiatan extends CActiveRecord
{
	public function tableName()
	{
		return 't_kegiatan';
	}

	public function rules()
	{
		return array(
			array('id_unitkerja, nama_kegiatan, id_satuan', 'required'),
			array('id_unitkerja, id_satuan, jml_target, is_ckp, id_pegawai_usulan', 'numerical', 'integerOnly'=>true),
			array('nama_kegiatan', 'length', 'max'=>128),
			array('id_jenis, id_unitkerja, tgl_mulai, tgl_selesai,  keterangan', 'safe'),

			array('id, id_jenis, nama_kegiatan, id_unitkerja, tgl_mulai, tgl_selesai, id_satuan, jml_target, keterangan, id_pegawai_usulan, created_on', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'unitkerja'=>array(self::BELONGS_TO,'MasterUnitkerja','id_unitkerja'),			
			'satuan'=>array(self::BELONGS_TO,'MasterSatuan','id_satuan'),
			'pegawai'=>array(self::HAS_MANY,'TabelTargetPegawai','id_kegiatan'),	
			'pegawai_usulan'=>array(self::BELONGS_TO,'MasterPegawai','id_pegawai_usulan'),	
			'mingguan'=>array(self::HAS_MANY,'TabelTargetMingguan','id_kegiatan'),	
			'realisasi'=>array(self::HAS_MANY,'TabelRealisasi','id_kegiatan'),	
			'lock'=>array(self::BELONGS_TO,'LockData',array('year(tgl_mulai)'=>'tahun','month(tgl_mulai)'=>'bulan')),
		);
	}

	public function getStr_tgl_mulai()
	{
		return strftime('%d %b %Y',strtotime($this->tgl_mulai));
	}

	public function getStr_tgl_mulai_2()
	{
		return date('d/m/Y',strtotime($this->tgl_mulai));
	}

	public function getStr_tgl_selesai()
	{
		return strftime('%d %b %Y',strtotime($this->tgl_selesai));
	}

	public function getStr_tgl_selesai_2()
	{
		return date('d/m/Y',strtotime($this->tgl_selesai));
	}

	public function getJadwal()
	{
		if($this->tgl_mulai==$this->tgl_selesai)
			return strftime('%d %b',strtotime($this->tgl_mulai));
		elseif(substr($this->tgl_mulai, 0,8)==substr($this->tgl_selesai, 0,8))
			return substr($this->tgl_mulai,8,2).' - '.strftime('%d %b',strtotime($this->tgl_selesai));
		else
			return strftime('%d %b',strtotime($this->tgl_mulai)).' - '.strftime('%d %b',strtotime($this->tgl_selesai));
	}

	public function getJadwal_tahun()
	{
		return $this->jadwal.' '.substr($this->tgl_selesai,0,4);
	}

	public function getTarget_satuan()
	{
		return $this->jml_target.' '.$this->satuan->nama_satuan;
	}

	public function getJml_realisasi()
	{
		return Yii::app()->db->createCommand("SELECT SUM(jml_realisasi) FROM ".TabelRealisasi::model()->tableName()." WHERE id_kegiatan=".$this->id)->queryScalar();
	}

	public function getChild_target()
	{
		return Yii::app()->db->createCommand("SELECT SUM(jml_target) FROM ".TabelTargetPegawai::model()->tableName()." WHERE id_kegiatan=".$this->id)->queryScalar();
	}

	public function getProgress()
	{
		if(!$this->jml_realisasi)
			return 0;
		else
			return number_format($this->jml_realisasi/$this->jml_target*100,1);
	}
	
	public function getJml_pgw()
	{
		return Yii::app()->db->createCommand("SELECT count(id_pegawai) FROM ".TabelTargetPegawai::model()->tableName()." WHERE id_kegiatan=".$this->id)->queryScalar();
	
	}

	public function getPenilaian()
	{
		return Yii::app()->db->createCommand("SELECT COUNT(id_pegawai) FROM ".TabelTargetPegawai::model()->tableName()." WHERE id_kegiatan=".$this->id." AND persen_kualitas<>0 AND persen_kualitas<>''")->queryScalar();		
	}

	public function getIs_lock()
	{
		return Yii::app()->db->createCommand("SELECT is_lock FROM ".LockData::model()->tableName()." WHERE tahun=".$this->tahun.' AND bulan='.$this->bulan)->queryScalar();
	}

	public function getTahun()
	{
		return substr($this->tgl_mulai,0,4);
	}

	public function getBulan()
	{
		return substr($this->tgl_mulai,5,2);
	}

	public function getFungsional($id)
	{
		return TabelKegiatanFungsional::model()->findByAttributes(array(
			'id_kegiatan'=>$this->id,
			'id_fungsional'=>substr($id,0,1),
		));
	}

	public function getTarget_pegawai($id_pegawai)
	{
		return TabelTargetPegawai::model()->findByAttributes(array('id_kegiatan'=>$this->id, 'id_pegawai'=>$id_pegawai));
	}

	public function getJenis()
	{
		switch($this->id_jenis){
			case 1: return 'Utama'; break;
			case 2: return 'Tambahan'; break;
		}
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nama_kegiatan' => 'Nama Kegiatan',
			'id_unitkerja' => 'Unitkerja',
			'tgl_mulai' => 'Tgl Mulai',
			'tgl_selesai' => 'Tgl Selesai',
			'id_satuan' => 'Satuan',
			'jml_target' => 'Jml Target',
			'keterangan' => 'Keterangan',
			'created_on' => 'Created On',
			'target_satuan' => 'Target',
			'child_target' => 'Dialokasikan',
			'is_ckp' => 'Tampil di Excel CKP',
			'id_jenis' => 'Jenis Kegiatan',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('nama_kegiatan',$this->nama_kegiatan,true);
		$criteria->compare('id_unitkerja',$this->id_unitkerja);
		$criteria->compare('tgl_mulai',$this->tgl_mulai,true);
		$criteria->compare('tgl_selesai',$this->tgl_selesai,true);
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

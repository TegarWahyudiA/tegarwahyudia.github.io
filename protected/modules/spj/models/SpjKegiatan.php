<?php

class SpjKegiatan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_spj';
	}

	public function rules()
	{
		return array(
			array('nama_kegiatan, id_unitkerja', 'required'),
			array('id_kegiatan, id_unitkerja, id_pegawai', 'numerical', 'integerOnly'=>true),
			array('tgl_selesai','length','min'=>10,'max'=>10),
			array('nama_kegiatan, id_kegiatan, id_unitkerja, progress', 'safe', 'on'=>'search'),
		);
	}
	public function relations()
	{
		return array(
			'kegiatan'=>array(self::BELONGS_TO, 'TabelKegiatan','id_kegiatan'),
			'pegawai'=>array(self::BELONGS_TO, 'MasterPegawai', 'id_pegawai'),
			'unitkerja'=>array(self::BELONGS_TO, 'MasterUnitkerja', 'id_unitkerja'),
			'dokumen'=>array(self::HAS_MANY,'SpjDokumen','id_spj'),
		);
	}

	public function getStr_tgl_selesai()
	{
		return strftime('%d %b %Y', strtotime($this->tgl_selesai));
	}

	public function getJml_berkas()
	{
		return Yii::app()->db->createCommand('SELECT COUNT(*) FROM '.SpjDokumen::model()->tableName().' WHERE id_spj='.$this->id)->queryScalar();
	}

	public function getJml_realisasi()
	{
		return Yii::app()->db->createCommand('SELECT SUM(status)/4 FROM '.SpjDokumen::model()->tableName().' WHERE id_spj='.$this->id)->queryScalar();
	}

	public function getProgress()
	{
		return $this->jml_berkas? number_format($this->jml_realisasi/$this->jml_berkas*100,0) : '-';
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nama_kegiatan'=>'Nama Kegiatan',
			'id_unitkerja'=>'Unitkerja',
			'id_pegawai' => 'Penanggungjawab',
			'tgl_selesai' =>'Deadline',
			'str_tgl_selesai' =>'Deadline',
			'jml_berkas' => 'Jml Berkas',
		);
	}

	public function search()
	{

		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('id_pegawai',$this->id_pegawai);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

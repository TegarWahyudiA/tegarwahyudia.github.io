<?php

class SpjSpj extends CActiveRecord
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
			array('id_spm, keperluan', 'required'),
			array('id_spm, no_urut', 'numerical', 'integerOnly'=>true),
			array('jumlah_kotor', 'numerical'),
			array('tanggal','length','min'=>10,'max'=>10),
			array('nomor, akun','length','max'=>16),
			array('keperluan','length','max'=>256),
			array('id_spm, keperluan, tanggal, nomor', 'safe', 'on'=>'search'),
		);
	}
	public function relations()
	{
		return array(
			'spm'=>array(self::BELONGS_TO, 'SpjSpm','id_spm'),
			'dokumen'=>array(self::HAS_MANY,'SpjDokumen','id_spj'),
		);
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

	public function getStr_jumlah_kotor()
	{
		return $this->jumlah_kotor? number_format($this->jumlah_kotor) : '';
	}

	public function getStr_tanggal()
	{
		if($this->tanggal)
			return date('d-m-Y', strtotime($this->tanggal));
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_spm' => 'SPM',
			'drpp' => 'DRPP',
			'nomor'=>'Nomor',
			'tanggal'=>'Tanggal',
			'keperluan' => 'Nama Penerima dan Keperluan',
			'akun' =>'Akun',
			'jumlah_kotor' =>'Jml Kotor',
			'str_jumlah_kotor' =>'Jml Kotor',
			'str_tanggal' => 'Tanggal',
		);
	}

	public function search()
	{

		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('id_spm',$this->id_spm);
		$criteria->order = 'no_urut, tanggal, nomor';
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

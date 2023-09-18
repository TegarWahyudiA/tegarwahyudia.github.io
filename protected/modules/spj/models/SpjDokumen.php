<?php

class SpjDokumen extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_spj_dokumen';
	}

	public function rules()
	{
		return array(
			array('id_spj, id_dokumen', 'required'),
			array('id_spj, id_dokumen, status, updated_on', 'numerical', 'integerOnly'=>true),
			array('tanggal', 'length', 'min'=>10, 'max'=>10),
			array('keterangan', 'length', 'max'=>64),
			array('id_dokumen', 'safe', 'on'=>'search'),
		);
	}
	public function relations()
	{
		return array(
			'spj'=>array(self::BELONGS_TO, 'SpjKegiatan','id_spj'),
			'dokumen'=>array(self::BELONGS_TO, 'SpjMaster','id_dokumen'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_spj' => 'Nama Kegiatan',
			'id_dokumen' => 'Dokumen',
			'str_tanggal' => 'Tanggal',
			'str_status' => 'Status',
		);
	}

	public function getStr_tanggal()
	{
		if($this->tanggal && $this->tanggal<>'0000-00-00')
			return strftime('%d %b', strtotime($this->tanggal));
		else 
			return '-';
	}

	public function getStr_status()
	{
		switch ($this->status) {
			case 0:
				return '-';	break;
			case 1:
				return 'Perlu Perbaikan';	break;
			case 2:
				return 'Sedang Diperiksa';	break;
			case 4:
				return BHtml::icon('ok',array('style'=>'color:green','title'=>'Disetujui'));	break;
		}
	}

	public function search()
	{

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_spj',$this->id_spj);
		$criteria->compare('id_dokumen',$this->id_dokumen);
		$criteria->order = "dokumen.dokumen";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

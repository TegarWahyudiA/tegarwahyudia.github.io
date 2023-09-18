<?php

class SpjSpm extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_spm';
	}

	public function rules()
	{
		return array(
			array('tanggal, nomor, jenis', 'required'),
			array('spp', 'numerical', 'integerOnly'=>true),
			array('nominal', 'numerical'),
			array('tanggal, drpp_tanggal','length','min'=>10,'max'=>10),
			array('nomor, jenis, drpp_nomor','length','max'=>16),
			array('keterangan','length','max'=>128),
			array('tanggal, nomor, jenis, keterangan', 'safe', 'on'=>'search'),
		);
	}
	public function relations()
	{
		return array(
			'spj'=>array(self::HAS_MANY,'SpjSpj','id_spm'),
		);
	}

	public function getStr_tanggal()
	{
		return date('d-m-Y', strtotime($this->tanggal));
	}

	public function getStr_nominal()
	{
		if($this->nominal)
			return number_format($this->nominal);
		else return;
	}

	public function getPersen()
	{
		return Yii::app()->db->createCommand("select persen from v_persen_spm where id_spm=".$this->id)->queryScalar();
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tanggal'=>'Tanggal',
			'nomor'=>'Nomor',
			'nominal' => 'Nominal',
			'str_nominal' => 'Nominal',
			'jenis' =>'Jenis',
			'spp' =>'Ada SPP',
			'drpp_tanggal' => 'Tanggal DRPP',
			'drpp_nomor' => 'Nomor DRPP',
			'keterangan' => 'Keterangan',
			'str_tanggal' => 'Tanggal'
		);
	}

	public function search()
	{

		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

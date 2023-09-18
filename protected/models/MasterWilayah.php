<?php


class MasterWilayah extends CActiveRecord
{
	public function tableName()
	{
		return 'm_wilayah';
	}

	public function rules()
	{
		return array(
			array('wilayah', 'required'),
			array('id', 'numerical', 'integerOnly'=>true),
			array('wilayah', 'length', 'max'=>32),

			array('id, wilayah', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'pegawai'=>array(self::HAS_MANY,'MasterPegawai','id_wilayah'),
		);
	}

	public function getKode_wilayah()
	{
		return $this->id.' - '.$this->wilayah;
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'wilayah' => 'Nama Wilayah',
			'kode_wilayah' => 'Kode Wilayah',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('wilayah',$this->wilayah,true);
		$criteria->order = "id";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

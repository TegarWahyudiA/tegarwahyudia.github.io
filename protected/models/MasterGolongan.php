<?php
class MasterGolongan extends CActiveRecord
{
	public function tableName()
	{
		return 'm_golongan';
	}


	public function rules()
	{
		return array(
			array('golongan', 'required'),
			array('golongan', 'length', 'max'=>32),
			array('id, golongan', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'golongan' => 'Golongan',
		);
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
}
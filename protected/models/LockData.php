<?php
class LockData extends CActiveRecord
{
	public function tableName()
	{
		return 't_lock';
	}

	public function rules()
	{
		return array(
			array('tahun, bulan, is_lock', 'required'),
			array('tahun, bulan, is_lock, updated_on', 'numerical', 'integerOnly'=>true),
			array('id, tahun, bulan, is_lock', 'safe', 'on'=>'search'),
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
			'tahun' => 'Tahun',
			'bulan' => 'Bulan',
			'is_lock' => 'Kunci Aktivitas',
			'updated_on' => 'Updated On',
		);
	}

	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('tahun',$this->tahun);
		$criteria->compare('bulan',$this->bulan);
		$criteria->compare('is_lock',$this->is_lock);
		$criteria->compare('updated_on',$this->updated_on,true);

		$criteria->order='tahun desc, bulan desc';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

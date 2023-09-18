<?php
class SplitPegawai extends CActiveRecord
{
	public function getDbConnection()
	{
		return Yii::app()->split_db;
	} 

	public function tableName()
	{
		return 'kk_pegawai';
	}

	public function rules()
	{
		return array(
			array('nama_pegawai, nipbaru, id_golongan, id_wilayah, id_unitkerja', 'required'),
			array('nipbaru', 'length', 'max'=>18),
			array('nama_pegawai', 'length', 'max'=>32),
			array('username', 'length', 'max'=>64),
			array('id_golongan, id_wilayah, id_unitkerja, id_eselon, id_fungsional', 'numerical', 'integerOnly'=>true),
			array('nipbaru', 'unique'),

			array('id, nipbaru, nama_pegawai, id_wilayah, id_unitkerja, id_eselon, id_fungsional, username', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'fungsional'=>array(self::BELONGS_TO, 'MasterFungsional', 'id_fungsional'),
		);
	}

	public function getPenilai()
	{
		if($this->id_eselon==3){
			$es2 = MasterPegawai::model()->find(array('condition'=>'id_eselon=2'));
			$es2->id = substr($es2->nip,0,4)=='3400'? (int) substr($es2->nip,-5) : $es2->nip;
			return $es2;
		} elseif($this->id_eselon==4)
			return SplitPegawai::model()->find(array(
				'condition'=>'id_eselon=3 AND id_unitkerja ='.substr($this->id_unitkerja, 0, 3).'0'
			));
		elseif($this->id_fungsional || $this->id_unitkerja == 9287)
			return SplitPegawai::model()->find(array(
				'condition'=>'id_eselon=3 AND id_unitkerja ='.substr($this->id_unitkerja, 0, 3).'0'
			));
		elseif($this->id_unitkerja < 9287)
			return SplitPegawai::model()->find(array(
				'condition'=>'id_eselon=4 AND id_unitkerja ='.$this->id_unitkerja
			));
		else 
			return;			
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'nipbaru' => 'NIP',
			'nama_pegawai' => 'Nama Pegawai',
			'id_golongan' => 'Golongan',
			'id_wilayah' => 'Wilayah',
			'id_unitkerja' => 'Unitkerja',
			'id_eselon' => 'Eselon',
			'id_fungsional' => 'Fungsional',
			'is_aktif' => 'Status',
			'username' => 'Username Login',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('nipbaru',$this->nipbaru,true);
		$criteria->compare('nama_pegawai',$this->nama_pegawai,true);
		$criteria->compare('id_wilayah;',$this->id_wilayah);
		$criteria->compare('id_golongan',$this->id_golongan);
		$criteria->compare('id_unitkerja',$this->id_unitkerja);
		$criteria->compare('id_eselon',$this->id_eselon);
		$criteria->compare('id_fungsional',$this->id_fungsional);

		$criteria->order='id_wilayah, case when id_eselon<2 then 9 else id_eselon end, id_unitkerja, nama_pegawai';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

}

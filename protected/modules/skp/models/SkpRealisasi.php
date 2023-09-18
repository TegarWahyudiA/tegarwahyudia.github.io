<?php

class SkpRealisasi extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_skp_realisasi';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_kegiatan, id_pegawai, tanggal', 'required'),
			array('id_kegiatan, jml_realisasi', 'numerical', 'integerOnly'=>true),
			array('id_pegawai', 'length', 'max'=>20),
			array('keterangan', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_kegiatan, id_pegawai, tanggal, jml_realisasi, keterangan, created_on', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'kegiatan'=>array(self::BELONGS_TO,'SkpKegiatan','id_kegiatan'),
			'pegawai'=>array(self::BELONGS_TO,'MasterPegawai','id_pegawai'),
		);
	}

	public function getStr_tgl()
	{
		return strftime("%d %b %Y",strtotime($this->tanggal));
	}
	
	public function getStr_realisasi()
	{
		return $this->jml_realisasi.' '.$this->kegiatan->satuan->nama_satuan;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_kegiatan' => 'Kegiatan',
			'id_pegawai' => 'Pegawai',
			'tanggal' => 'Tanggal',
			'jml_realisasi' => 'Jml Realisasi',
			'keterangan' => 'Keterangan',
			'created_on' => 'Created On',
			'str_tgl'=>'Tanggal',
			'str_realisasi'=>'Realisasi',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('id_kegiatan',$this->id_kegiatan);
		$criteria->compare('id_pegawai',$this->id_pegawai,true);
		$criteria->compare('tgl',$this->tgl,true);
		$criteria->compare('jml_realisasi',$this->jml_realisasi);
		$criteria->compare('keterangan',$this->keterangan,true);
		$criteria->compare('created_on',$this->created_on,true);
		$criteria->compare('acc_on',$this->acc_on,true);

		$criteria->order='tgl ASC';

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TabelRealisasi the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

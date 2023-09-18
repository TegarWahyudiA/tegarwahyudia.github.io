<?php

class SkpPegawai extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_skp_pegawai';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_kegiatan, id_pegawai, jml_target', 'required'),
			array('id_kegiatan, jml_target, persen_kualitas, id_fungsional, kode_kredit', 'numerical', 'integerOnly'=>true),
			array('id_pegawai', 'length', 'max'=>20),
			array('keterangan', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_kegiatan, id_pegawai, jml_target, persen_kualitas, keterangan, created_on', 'safe', 'on'=>'search'),
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
			'pegawai'=>array(self::BELONGS_TO,'MasterPegawai','id_pegawai'),
			'kegiatan'=>array(self::BELONGS_TO,'SkpKegiatan','id_kegiatan'),
			'fungsional'=>array(self::BELONGS_TO,'MasterFungsional','id_fungsional'),
//			'kegiatan_fungsional'=>array(self::BELONGS_TO,'TabelKegiatanFungsional','id_kegiatan'),
		);
	}

	public function getJml_realisasi()
	{
		return Yii::app()->db->createCommand("SELECT SUM(jml_realisasi) FROM ".SkpRealisasi::model()->tableName()." WHERE id_kegiatan=".$this->id_kegiatan." AND id_pegawai=".$this->id_pegawai)->queryScalar();
	}

	public function getRealisasi()
	{
		return SkpRealisasi::model()->findAllByAttributes(array('id_kegiatan'=>$this->id_kegiatan,'id_pegawai'=>$this->id_pegawai));
	}

	public function getKredit()
	{
		return MasterKredit::model()->findByAttributes(array('kode_tingkat'=>substr($this->id_fungsional,0,1),'kode'=>$this->kode_kredit));
	}

	public function getTarget_satuan()
	{
		return $this->jml_target.' '.$this->kegiatan->satuan->nama_satuan;
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
			'jml_target' => 'Jml Target',
			'jml_realisasi' => 'Jml Realisasi',
			'persen_kualitas' => 'Persen Kualitas',
			'keterangan' => 'Keterangan',
			'created_on' => 'Created On',
			'target_satuan' =>'Target',
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
		$criteria->compare('jml_target',$this->jml_target);
		$criteria->compare('persen_kualitas',$this->persen_kualitas);
		$criteria->compare('keterangan',$this->keterangan,true);
		$criteria->compare('created_on',$this->created_on,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TabelTargetPegawai the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

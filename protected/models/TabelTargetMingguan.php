<?php

/**
 * This is the model class for table "t_target_mingguan".
 *
 * The followings are the available columns in table 't_target_mingguan':
 * @property integer $id
 * @property integer $id_sub_kegiatan
 * @property integer $id_pegawai
 * @property string $deadline
 * @property integer $jml_target
 * @property string $keterangan
 */
class TabelTargetMingguan extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_target_mingguan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_kegiatan, id_pegawai, mingguke, jml_target', 'required'),
			array('id_kegiatan, id_pegawai, mingguke, jml_target', 'numerical', 'integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_kegiatan, id_pegawai, jml_target, keterangan', 'safe', 'on'=>'search'),
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
			'kegiatan'=>array(self::BELONGS_TO,'TabelKegiatan','id_kegiatan'),
			'pegawai'=>array(self::BELONGS_TO,'MasterPegawai','id_pegawai'),
		);
	}

	public function getTarget()
	{
		return TabelTargetPegawai::model()->findByAttributes(array(
			'id_kegiatan'=>$this->id_kegiatan,
			'id_pegawai'=>$this->id_pegawai,
		));
	}

	public function getTarget_satuan()
	{
		return $this->jml_target.' '.$this->kegiatan->satuan->nama_satuan;
	}

	public function getStr_periode()
	{
		$tahun = substr($this->kegiatan->tgl_mulai,4);
		return strftime('%d %b',strtotime($tahun.'W'.$this->mingguke.'1')).' - '.
			strftime('%d %b',strtotime($tahun.'W'.$this->mingguke.'5'));
	}

	public function getJml_realisasi()
	{
		return Yii::app()->db->createCommand("SELECT SUM(jml_realisasi) FROM ".TabelRealisasi::model()->tableName()." WHERE id_kegiatan=".$this->id_kegiatan.' AND id_pegawai='.$this->id_pegawai.' AND week(tgl)='.$this->mingguke)->queryScalar();
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
			'mingguke' => 'Mingguan',
			'jml_target' => 'Jml Target',
			'keterangan' => 'Keterangan',
			'target_satuan' => 'Target',
			'str_periode'=>'Periode',
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
		$criteria->compare('id_pegawai',$this->id_pegawai);
		$criteria->compare('mingguke',$this->mingguke);
		$criteria->compare('jml_target',$this->jml_target);
		$criteria->compare('keterangan',$this->keterangan,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return TabelTargetMingguan the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

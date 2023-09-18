<?php

/**
 * This is the model class for table "m_seksi".
 *
 * The followings are the available columns in table 'm_seksi':
 * @property string $id
 * @property string $seksi
 */
class MasterUnitkerja extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'm_unitkerja';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id, unitkerja', 'required'),
			array('id', 'length', 'max'=>20),
			array('unitkerja', 'length', 'max'=>32),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, unitkerja', 'safe', 'on'=>'search'),
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
			'kegiatan'=>array(self::HAS_MANY,'TabelKegiatan','id_seksi'),
			'pegawai'=>array(self::HAS_MANY,'MasterPegawai','id_seksi'),
			'persen'=>array(self::HAS_MANY,'PersenSeksi','id_seksi'),
		);
	}

	public function getPersen($tahun, $bulan)
	{
		$return = Yii::app()->db->createCommand("SELECT persen_kuantitas FROM v_persen_unitkerja WHERE id_unitkerja=".$this->id." AND tahun=".$tahun." AND bulan=".$bulan)->queryScalar();
		return number_format($return,1);
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'unitkerja' => 'Unitkerja',
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

		$criteria->compare('id',$this->id,true);
		$criteria->compare('seksi',$this->seksi,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MasterSeksi the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

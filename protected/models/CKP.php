<?php

/**
 * This is the model class for table "t_ckp".
 *
 * The followings are the available columns in table 't_ckp':
 * @property integer $id
 * @property integer $tahun
 * @property integer $bulan
 * @property integer $id_pegawai
 * @property double $r_kuantitas
 * @property double $r_kualitas
 * @property double $nilai_ckp
 * @property string $updated_on
 */
class CKP extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 't_ckp';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('tahun, bulan, id_pegawai, r_kuantitas, r_kualitas, nilai_ckp', 'required'),
			array('tahun, bulan, id_pegawai', 'numerical', 'integerOnly'=>true),
			array('r_kuantitas, r_kualitas, nilai_ckp, angka_kredit', 'numerical'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, tahun, bulan, id_pegawai, updated_on', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'tahun' => 'Tahun',
			'bulan' => 'Bulan',
			'id_pegawai' => 'Pegawai',
			'r_kuantitas' => 'Kuantitas',
			'r_kualitas' => 'Kualitas',
			'nilai_ckp' => 'Nilai CKP',
			'angka_kredit' => 'Angka Kredit',
			'updated_on' => 'Updated On',
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
		$criteria->compare('tahun',$this->tahun);
		$criteria->compare('bulan',$this->bulan);
		$criteria->compare('id_pegawai',$this->id_pegawai);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CKP the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

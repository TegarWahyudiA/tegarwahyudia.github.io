<?php

/**
 * This is the model class for table "a_shift".
 *
 * The followings are the available columns in table 'a_shift':
 * @property integer $id
 * @property string $keterangan
 * @property string $jadwal_masuk
 * @property string $jadwal_pulang
 */
class CalendarShift extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'a_shift';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('keterangan, jadwal_masuk, jadwal_pulang', 'required'),
			array('keterangan', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, keterangan, jadwal_masuk, jadwal_pulang', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'keterangan' => 'Keterangan',
			'jadwal_masuk' => 'Jadwal Masuk',
			'jadwal_pulang' => 'Jadwal Pulang',
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
		$criteria->compare('keterangan',$this->keterangan,true);
		$criteria->compare('jadwal_masuk',$this->jadwal_masuk,true);
		$criteria->compare('jadwal_pulang',$this->jadwal_pulang,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CalendarShift the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

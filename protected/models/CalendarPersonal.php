<?php

/**
 * This is the model class for table "a_personalcalendar".
 *
 * The followings are the available columns in table 'a_personalcalendar':
 * @property integer $id
 * @property string $FingerPrintID
 * @property string $PersonalCalendarDate
 * @property string $TimeCome
 * @property string $TimeHome
 * @property integer $LateIn
 * @property integer $EarlyOut
 * @property integer $PersonalCalendarStatus
 * @property string $PersonalCalendarReason
 */
class CalendarPersonal extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'a_personalcalendar';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('FingerPrintID, PersonalCalendarDate', 'required'),
			array('LateIn, EarlyOut, PersonalCalendarStatus', 'numerical', 'integerOnly'=>true),
			array('FingerPrintID', 'length', 'max'=>30),
			array('PersonalCalendarReason', 'length', 'max'=>100),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, FingerPrintID, PersonalCalendarDate, TimeCome, TimeHome, LateIn, EarlyOut, PersonalCalendarStatus, PersonalCalendarReason', 'safe', 'on'=>'search'),
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
			'status'=>array(self::BELONGS_TO,'CalendarStatus','PersonalCalendarStatus'),
			'holiday'=>array(self::BELONGS_TO,'CalendarHoliday','PersonalCalendarDate'),
			'pegawai'=>array(self::BELONGS_TO,'MasterPegawai', array('FingerPrintID'=>'id_presensi')),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'FingerPrintID' => 'Finger Print',
			'PersonalCalendarDate' => 'Personal Calendar Date',
			'TimeCome' => 'Time Come',
			'TimeHome' => 'Time Home',
			'LateIn' => 'Late In',
			'EarlyOut' => 'Early Out',
			'PersonalCalendarStatus' => 'Status',
			'PersonalCalendarReason' => 'Keterangan',
		);
	}

	public function getTanggal()
	{
		return strftime('%d %b %Y', strtotime($this->PersonalCalendarDate));
	}

	public function getHari()
	{
		return strftime('%A',strtotime($this->PersonalCalendarDate));
	}

	public function getDatang_jam()
	{
		if(!$this->TimeCome) return null;
		$arr = explode(':', $this->TimeCome);
		return $arr[0];
	}

	public function getDatang_menit()
	{
		if(!$this->TimeCome) return null;
		$arr = explode(':', $this->TimeCome);
		return $arr[1];
	}

	public function getPulang_jam()
	{
		if(!$this->TimeHome) return null;
		$arr = explode(':', $this->TimeHome);
		return $arr[0];
	}

	public function getPulang_menit()
	{
		if(!$this->TimeHome) return null;
		$arr = explode(':', $this->TimeHome);
		return $arr[1];
	}

	public function getTelat_jam()
	{
		if($this->LateIn) return floor($this->LateIn/60);
	}

	public function getTelat_menit()
	{
		if($this->LateIn) return $this->LateIn%60;
	}

	public function getPsw_jam()
	{
		if($this->EarlyOut) return floor($this->EarlyOut/60);
	}

	public function getPsw_menit()
	{
		if($this->EarlyOut) return $this->EarlyOut%60;
	}

	public function getKeterangan()
	{
		return $this->PersonalCalendarReason;
	}

	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('FingerPrintID',$this->FingerPrintID,true);
		$criteria->compare('PersonalCalendarDate',$this->PersonalCalendarDate,true);
		$criteria->compare('TimeCome',$this->TimeCome,true);
		$criteria->compare('TimeHome',$this->TimeHome,true);
		$criteria->compare('LateIn',$this->LateIn);
		$criteria->compare('EarlyOut',$this->EarlyOut);
		$criteria->compare('PersonalCalendarStatus',$this->PersonalCalendarStatus);
		$criteria->compare('PersonalCalendarReason',$this->PersonalCalendarReason,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CalendarPersonal the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

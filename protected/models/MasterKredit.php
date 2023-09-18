<?php
class MasterKredit extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'm_kredit';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('kode, kode_tingkat', 'numerical', 'integerOnly'=>true),
			array('tingkat', 'length', 'max'=>8),
			array('kode_perka', 'length', 'max'=>9),
			array('kode_unsur', 'length', 'max'=>3),
			array('unsur', 'length', 'max'=>40),
			array('uraian_singkat', 'length', 'max'=>302),
			array('kegiatan', 'length', 'max'=>119),
			array('satuan_hasil', 'length', 'max'=>45),
			array('bukti_fisik', 'length', 'max'=>358),
			array('angka_kredit, pelaksana', 'length', 'max'=>6),
			array('pelaksana_kegiatan', 'length', 'max'=>18),
			array('keterangan', 'length', 'max'=>939),
			array('pelaksana_lanjutan, penyelia, pertama, muda, madya', 'length', 'max'=>5),
			array('bidang', 'length', 'max'=>10),
			array('seksi', 'length', 'max'=>32),

			array('id, kode, tingkat, kode_tingkat, kode_perka, kode_unsur, unsur, uraian_singkat, kegiatan, satuan_hasil, bukti_fisik, angka_kredit, pelaksana_kegiatan, keterangan, pelaksana, pelaksana_lanjutan, penyelia, pertama, muda, madya, bidang, seksi, term', 'safe', 'on'=>'search'),
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

	public function getSimple()
	{
		return $this->kode.' - '.$this->uraian_singkat.' :: '.$this->kegiatan;
	}

	public function getTerm()
	{

	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'kode' => 'Kode',
			'tingkat' => 'Tingkat',
			'kode_tingkat' => 'Kode Tingkat',
			'kode_perka' => 'Kode Perka',
			'kode_unsur' => 'Kode Unsur',
			'unsur' => 'Unsur',
			'uraian_singkat' => 'Uraian Singkat',
			'kegiatan' => 'Kegiatan',
			'satuan_hasil' => 'Satuan Hasil',
			'bukti_fisik' => 'Bukti Fisik',
			'angka_kredit' => 'Angka Kredit',
			'pelaksana_kegiatan' => 'Pelaksana Kegiatan',
			'keterangan' => 'Keterangan',
			'pelaksana' => 'Pelaksana',
			'pelaksana_lanjutan' => 'Pelaksana Lanjutan',
			'penyelia' => 'Penyelia',
			'pertama' => 'Pertama',
			'muda' => 'Muda',
			'madya' => 'Madya',
			'bidang' => 'Bidang',
			'seksi' => 'Seksi',
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
		$criteria->compare('kode',$this->kode);
		$criteria->compare('tingkat',$this->tingkat,true);
		$criteria->compare('kode_tingkat',$this->kode_tingkat);
		$criteria->compare('kode_perka',$this->kode_perka,true);
		$criteria->compare('kode_unsur',$this->kode_unsur,true);
		$criteria->compare('unsur',$this->unsur,true);
		$criteria->compare('uraian_singkat',$this->uraian_singkat,true);
		$criteria->compare('kegiatan',$this->kegiatan,true);
		$criteria->compare('satuan_hasil',$this->satuan_hasil,true);
		$criteria->compare('bukti_fisik',$this->bukti_fisik,true);
		$criteria->compare('angka_kredit',$this->angka_kredit,true);
		$criteria->compare('pelaksana_kegiatan',$this->pelaksana_kegiatan,true);
		$criteria->compare('keterangan',$this->keterangan,true);
		$criteria->compare('pelaksana',$this->pelaksana,true);
		$criteria->compare('pelaksana_lanjutan',$this->pelaksana_lanjutan,true);
		$criteria->compare('penyelia',$this->penyelia,true);
		$criteria->compare('pertama',$this->pertama,true);
		$criteria->compare('muda',$this->muda,true);
		$criteria->compare('madya',$this->madya,true);
		$criteria->compare('bidang',$this->bidang,true);
		$criteria->compare('seksi',$this->seksi,true);

//$criteria->condition = "concat(uraian_singkat,' ',kegiatan) LIKE '%".$this->term."%'";

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MasterFungsional the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}

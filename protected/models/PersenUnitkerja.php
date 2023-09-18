<?php
class PersenUnitkerja extends CActiveRecord
{
	public function tableName()
	{
		return 'v_persen_unitkerja';
	}


	public function rules()
	{
		return array(
			array('tahun, bulan, id_unitkerja, persen_kuantitas', 'required'),
			array('tahun, bulan, id_unitkerja', 'integer'),
			array('tahun, bulan, id_unitkerja', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'unitkerja'=>array(self::BELONGS_TO,'MasterUnitkerja','id_unitkerja'),
		);
	}
}
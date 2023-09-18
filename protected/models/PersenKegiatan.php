<?php
class PersenKegiatan extends CActiveRecord
{
	public function tableName()
	{
		return 'v_persen_kegiatan';
	}


	public function rules()
	{
		return array(
			array('id, tahun, bulan, id_seksi, persen_kuantitas', 'required'),
			array('id, tahun, bulan, id_seksi,', 'integer'),
			array('id, tahun, bulan, id_seksi,', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'kegiatan'=>array(self::BELONGS_TO,'TabelKegiatan','id'),
			'seksi'=>array(self::BELONGS_TO,'MasterSeksi','id_seksi'),
		);
	}
}
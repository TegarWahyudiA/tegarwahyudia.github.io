<?php 
class TabelBbm extends CActiveRecord
{
	public function tableName()
	{
		return 't_bbm';
	}

	public function rules()
	{
		return array(
			array('id_pegawai, tanggal, id_jenis, nilai', 'required'),
			array('id_pegawai, id_jenis', 'numerical', 'integerOnly'=>true),
			array('tanggal', 'length', 'min'=>10, 'max'=>10),
			array('nilai', 'numerical'),
			array('keterangan', 'length', 'max'=>64),

			array('id, id_pegawai, tanggal, id_jenis, keterangan', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'pegawai'=>array(self::BELONGS_TO, 'MasterPegawai', 'id_pegawai'),
		);
	}

	public function getJenis()
	{
		switch ($this->id_jenis) {
			case 1: return 'Nota Bensin'; break;
			case 2:	return 'Nota Bengkel'; break;
			default: return ''; break;
		}
	}

	public function getStr_tanggal()
	{
		return strftime('%d %b %Y',strtotime($this->tanggal));
	}

	public function getStr_nilai()
	{
		return 'Rp. '.number_format($this->nilai).',-';
	}
	
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_pegawai' => 'Pegawai',
			'tanggal' => 'Tanggal Nota',
			'id_jenis' => 'Jenis Nota',
			'nilai' => 'Nilai Rp.',
			'keterangan' => 'Keterangan',
			'str_tanggal' => 'Tanggal',
			'jenis' => 'Jenis Nota',
			'str_nilai' => 'Nilai',
		);
	}
}

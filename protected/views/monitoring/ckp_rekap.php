<?php
$this->pageCaption=Yii::app()->user->unitkerja;
$this->pageTitle=$this->pageCaption;

$prev_bulan = ($bulan==1)? array('ckp_rekap','tahun'=>$tahun-1,'bulan'=>12) : array('ckp_rekap','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('ckp_rekap','tahun'=>$tahun+1,'bulan'=>1) : array('ckp_rekap','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Monitoring',//=>array('index','tahun'=>$tahun,'bulan'=>$bulan),
	'Rekap CKP',
);

$this->widget('TbGridView', array(
	'id'=>'tabel-ckp-grid',
	'dataProvider'=>$dataProvider,
	'cssFile'=>Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('ext.bootstrap-theme.widgets.assets')).'/gridview/styles.css',
	'itemsCssClass'=>'table',
	'border'=>$excel?'1':'',
	'template'=>'{items}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('name'=>'nama_pegawai','type'=>'raw','value'=>function($data)use($excel){
			if(!$excel && (!$data->pegawai->id_eselon || Yii::app()->user->id_eselon<$data->pegawai->id_eselon || (Yii::app()->user->id_eselon==$data->pegawai->id_eselon && Yii::app()->user->id_unitkerja==$data->pegawai->id_unitkerja) ) )
				return CHtml::link($data->pegawai->nama_pegawai,array('pegawai','id'=>$data->id_pegawai, 'bulan'=>$data->bulan, 'tahun'=>$data->tahun));
			else
				return $data->pegawai->nama_pegawai;
		}),
		array('header'=>'Jabatan/Unitkerja','value'=>function($data){return $data->pegawai->jabatan;}),
		array('header'=>'Jml Kegiatan', 'value'=>function($data){return $data->jml_kegiatan;}),
		array('header'=>'Kuantitas', 'value'=>function($data){return $data->r_kuantitas;}),
		array('header'=>'Kualitas','value'=>function($data){return $data->r_kualitas;}),
		array('header'=>'Nilai CKP','value'=>function($data){return $data->nilai_ckp;}),
		array('header'=>'Angka Kredit','value'=>function($data){return $data->pegawai->id_fungsional? $data->angka_kredit : '-';}),
//		array('name'=>'updated_on','visible'=>!$excel),
	),
)); 

if(!$excel){
	echo CHtml::link('<i class="g g-download"></i> Export Excel',array('ckp_rekap','bulan'=>$bulan,'tahun'=>$tahun,'excel'=>'yes'));
}


<?php
$this->pageTitle='Sakit, Ijin, Cuti, DL';
$this->pageCaption=$this->pageTitle;
$this->breadcrumbs=array(
	'Administrator',
	'Presensi',
);

$prev_bulan = ($bulan==1)? array('absen','tahun'=>$tahun-1,'bulan'=>12) : array('absen','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('absen','tahun'=>$tahun+1,'bulan'=>1) : array('absen','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';


$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>array('index')),
		array('label'=>'Proses','url'=>array('proses')),
		array('label'=>'Rekap Bulanan','url'=>array('rekap')),
		array('label'=>'Ketidakhadiran','url'=>'#','active'=>true),
		array('label'=>'Perubahan Jam','url'=>array('jam')),
		array('label'=>'Hari Libur','url'=>array('libur')),
	)
));

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('header'=>'Nama Pegawai','type'=>'raw','value'=>function($data)use($tahun){ return CHtml::link($data->pegawai->nama_pegawai,array('absen_pegawai','id'=>$data->pegawai->id,'tahun'=>$tahun));}),
		'tanggal',
		array('name'=>'PersonalCalendarReason','header'=>'Keterangan'),
		array('header'=>'','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
			return CHtml::link(BHtml::icon('pencil'),array('absen_update','id'=>$data->pegawai->id,'tanggal'=>$data->PersonalCalendarDate));
		})
	),
	));

echo CHtml::link(BHtml::icon('plus-sign').'Tambah Data',array('absen_create'));


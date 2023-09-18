<?php
$this->pageCaption = $model->nama_pegawai;
$this->pageTitle=$this->pageCaption;


$prev = array('absen_pegawai','id'=>$model->id,'tahun'=>$tahun-1);
$next = array('absen_pegawai','id'=>$model->id,'tahun'=>$tahun+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev).' '.$tahun.' '.CHtml::link('<i class="g g-chevron-right"></i>',$next).'</span>';

$this->breadcrumbs=array(
	'Administrator',
//	'Presensi',//=>array('index'),
	'Presensi',
);

$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>array('index')),
		array('label'=>'Proses','url'=>array('proses')),
		array('label'=>'Rekap Bulanan','url'=>array('rekap')),
		array('label'=>'Ketidakhadiran','url'=>array('absen'),'active'=>true),
		array('label'=>'Perubahan Jam','url'=>array('jam')),
		array('label'=>'Hari Libur','url'=>array('libur')),
	)
));


$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		'tanggal',
		'status.PersonalCalendarStatus',
		'PersonalCalendarReason'
	)
));

echo CHtml::link(BHtml::icon('menu-left').' Kembali',array('absen','tahun'=>$tahun));
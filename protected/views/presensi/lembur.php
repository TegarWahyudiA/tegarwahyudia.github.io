<?php
$this->pageTitle='Pengelolaan Hari Libur';
$this->pageCaption=$this->pageTitle;
$this->breadcrumbs=array(
	'Administrator',
	'Presensi',
);

$prev = array('libur','tahun'=>$tahun-1);
$next = array('libur','tahun'=>$tahun+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev).' '.$tahun.' '.CHtml::link('<i class="g g-chevron-right"></i>',$next).'</span>';


$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>array('index')),
		array('label'=>'Proses','url'=>array('proses')),
		array('label'=>'Rekap Bulanan','url'=>array('rekap')),
		array('label'=>'Ketidakhadiran','url'=>array('absen')),
		array('label'=>'Perubahan Jam','url'=>array('jam')),
		array('label'=>'Lembur','url'=>array('lembur'),'active'=>true),
		array('label'=>'Hari Libur','url'=>array('libur')),
	)
));

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		array('name'=>'tanggal','headerHtmlOptions'=>array('style'=>'width:80px')),
		array('name'=>'pegawai.nama_pegawai','header'=>'Nama Pegawai','headerHtmlOptions'=>array('style'=>'width:170px')),
		array('name'=>'TimeCome', 'header'=>'Datang','headerHtmlOptions'=>array('style'=>'width:60px')),
		array('name'=>'TimeHome', 'header'=>'Pulang','headerHtmlOptions'=>array('style'=>'width:60px')),
/*		array('header'=>'Jumlah','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:60px'),'value'=>function($data){
			if($data->TimeCome && $data->TimeHome)
				return date('H:i', strtotime($data->TimeHome)-strtotime($data->TimeCome));
		}),
*/		array('name'=>'PersonalCalendarReason', 'header'=>'Keterangan'),
		array('header'=>'','type'=>'raw','headerHtmlOptions'=>array('style'=>'width:20px'),'value'=>function($data){
			return CHtml::link(BHtml::icon('trash'),'#',array('title'=>'Hapus','submit'=>array('lembur_delete','tanggal'=>$data->PersonalCalendarDate), 'confirm' => 'Are you sure?', 'csrf'=>true));
		})
	),
	));

echo CHtml::link(BHtml::icon('plus-sign').'Tambah Data',array('lembur_create'));


<?php
$this->pageCaption='Usulan Kegiatan';
$this->pageTitle=$this->pageCaption;

$prev_bulan = ($bulan==1)? array('usulan','tahun'=>$tahun-1,'bulan'=>12) : array('usulan','tahun'=>$tahun,'bulan'=>$bulan-1);
$next_bulan = ($bulan==12)? array('usulan','tahun'=>$tahun+1,'bulan'=>1) : array('usulan','tahun'=>$tahun,'bulan'=>$bulan+1);

$this->pageDescription = '<span style="float:right">'.CHtml::link('<i class="g g-chevron-left"></i>',$prev_bulan).' '.strftime('%B %Y',mktime(0,0,0,$bulan,1,$tahun)).' '.CHtml::link('<i class="g g-chevron-right"></i>',$next_bulan).'</span>';

$this->breadcrumbs=array(
	'Indeks Kegiatan'=>array('/kegiatan'),
	'Kegiatan Saya'=>array('/personal'),
);

$this->widget('TbGridView', array(
	'dataProvider'=>$dataProvider,
	'template'=>'{items} {pager}',
	'columns'=>array(
		array('class'=>'IndexColumn'),
		'nama_kegiatan',
		'jadwal',
		'target_satuan',
		'jenis',
		array('headerHtmlOptions'=>array('style'=>'width:40px'),'type'=>'raw','value'=>function($data){
			return CHtml::link(BHtml::icon('pencil'),array('usulan_edit','id'=>$data->id),array('title'=>'Edit Kegiatan Ini')).' '.
				CHtml::link(BHtml::icon('copy'),array('usulan_copy','id'=>$data->id),array('title'=>'Copy Kegiatan Ini'));
		})
	)
));

echo CHtml::link(BHtml::icon('plus-sign').'Usulan Baru',array('usulan_add'));
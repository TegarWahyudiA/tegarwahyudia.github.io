<?php
$this->pageCaption='Detail Angka Kredit';
$this->pageTitle=$this->pageCaption;
$this->breadcrumbs=array(
	'Master Angka Kredit'=>array('kredit'),
	$model->tingkat,
	$model->kode_perka
);

$prev = $model->id > 1? CHtml::link(BHtml::icon('chevron-left'),array('kredit_view','id'=>$model->id-1)) : '';
$next = $model->id < MasterKredit::model()->count()? CHtml::link(BHtml::icon('chevron-right'),array('kredit_view','id'=>$model->id+1)) : '';
$this->pageDescription = '<span style="float:right">'.$prev.' '.$next.'</span>';

$kolom_kredit = MasterFungsional::model()->findByPk(Yii::app()->user->id_fungsional)->kolom_kredit;

$this->widget('TbDetailView', array(
	'data'=>$model,
	'attributes'=>array(
//		'id',
		'kode',
//		'tingkat',
//		'kode_tingkat',
		'kode_perka',
//		'kode_unsur',
		'unsur',
		'kegiatan',
		'uraian_singkat',
//		'angka_kredit',
		'pelaksana_kegiatan',
		'keterangan',
		'bukti_fisik',
		'satuan_hasil',
		array('label'=>'Angka Kredit','name'=>$kolom_kredit),
/*		'pelaksana',
		'pelaksana_lanjutan',
		'penyelia',
		'pertama',
		'muda',
		'madya',
		'bidang',
		'seksi',
*/	)
));

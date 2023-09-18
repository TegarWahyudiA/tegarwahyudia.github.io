<?php
$this->pageCaption='Detail Angka Kredit';
$this->pageTitle=$this->pageCaption;
$this->breadcrumbs=array(
	'Master Angka Kredit'=>array('kredit'),
	$model->kode_perka.' - '.$model->uraian_singkat
);

$prev = $model->id > 1? CHtml::link(BHtml::icon('chevron-left'),array('kredit_view','id'=>$model->id-1)) : '';
$next = $model->id < MasterKredit::model()->count()? CHtml::link(BHtml::icon('chevron-right'),array('kredit_view','id'=>$model->id+1)) : '';
$this->pageDescription = '<span style="float:right">'.$prev.' '.$next.'</span>';

$this->widget('TbDetailView', array(
	'data'=>$model
));

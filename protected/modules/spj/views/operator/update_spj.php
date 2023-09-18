<?php
$this->pageCaption = 'Update SPJ';
$this->pageDescription = '';

$this->breadcrumbs=array(
	'Monitoring SPJ'=>array('index'),
	'SPM No. '.$model->spm->nomor.', Tgl. '.$model->spm->str_tanggal=>array('spm','id'=>$model->id_spm),
	'Update SPJ'
);

echo $this->renderPartial('_spj', array('model'=>$model));

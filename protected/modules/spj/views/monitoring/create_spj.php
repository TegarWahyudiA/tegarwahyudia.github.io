<?php
$this->pageCaption = 'Create SPJ';
$this->pageDescription = '';

$this->breadcrumbs=array(
	'Monitoring SPJ'=>array('index'),
	'SPM No. '.$model->spm->nomor.', Tgl. '.$model->spm->str_tanggal=>array('spm','id'=>$model->id_spm),
);

echo $this->renderPartial('_spj', array('model'=>$model));

echo CHtml::link(BHtml::icon('upload').'Import Excel SPJ',array('import_spj','spm'=>$model->id_spm),array('style'=>'float:right'));
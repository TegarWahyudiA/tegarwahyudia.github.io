<?php
$this->pageCaption = 'Input SPM';
$this->pageDescription = '';

$this->breadcrumbs=array(
	'Monitoring SPJ',
	'Tambah Kegiatan',
);

echo $this->renderPartial('_spm', array('model'=>$model));
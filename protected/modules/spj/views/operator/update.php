<?php
$this->pageCaption = 'Monitoring SPJ';
$this->pageDescription = '';

$this->breadcrumbs=array(
	'Monitoring SPJ'=>array('index'),
	'Update Kegiatan',
);

echo $this->renderPartial('_form', array('model'=>$model));
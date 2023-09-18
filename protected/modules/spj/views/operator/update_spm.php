<?php
$this->pageCaption = 'Update SPM';
$this->pageDescription = '';

$this->breadcrumbs=array(
	'Monitoring SPJ',
	'Update SPM',
);

echo $this->renderPartial('_spm', array('model'=>$model));
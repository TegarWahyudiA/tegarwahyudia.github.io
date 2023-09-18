<?php
$this->pageCaption='View PersonalCalendar #'.$model->id;
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Personal Calendars'=>array('admin'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Personal Calendars', 'url'=>array('index')),
	array('label'=>'Create PersonalCalendar', 'url'=>array('create')),
	array('label'=>'Update PersonalCalendar', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete PersonalCalendar', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Personal Calendars', 'url'=>array('admin')),
);
?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>false,
	'cssFile'=>false,
	'attributes'=>array(
		'id',
		'FingerPrintID',
		'PersonalCalendarDate',
		'TimeCome',
		'TimeHome',
		'LateIn',
		'EarlyOut',
		'PersonalCalendarReason',
	),
)); ?>

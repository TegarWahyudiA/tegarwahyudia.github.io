<?php
$this->pageCaption='Update';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Administrator',
	'Presensi'=>array('proses'),
	'Ketidakhadiran'=>array('absen'),
	'Update',
);
$arr_status = array(''=>'-- Pilih --') + CHtml::listData(CalendarStatus::model()->findAll(array('order'=>'PersonalCalendarStatus')),'id','PersonalCalendarStatus');

$form=$this->beginWidget('BActiveForm', array(
	'id'=>'personal-calendar-form',
	'enableAjaxValidation'=>false,
)); 

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'baseScriptUrl'=>false,
	'cssFile'=>false,
	'attributes'=>array(
		'pegawai.nama_pegawai',
		'tanggal',
		array('label'=>'Status','type'=>'raw','value'=>$form->dropDownList($model,'PersonalCalendarStatus',$arr_status)),
		array('label'=>'Keterangan','type'=>'raw','value'=>$form->textField($model,'PersonalCalendarReason',array('size'=>60,'maxlength'=>100))),
		array('label'=>'','type'=>'raw','value'=>BHtml::submitButton('Save')),
	)
));

$this->endWidget(); ?>

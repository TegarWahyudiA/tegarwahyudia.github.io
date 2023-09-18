<?php
$this->pageCaption='Tambah Data';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Administrator',
	'Presensi',
	'Ketidakhadiran'=>array('absen')
);

$this->widget('TbMenu', array(
	'type'=>'tabs',
	'items'=>array(
		array('label'=>'Presensi Harian','url'=>array('index')),
		array('label'=>'Proses','url'=>array('proses')),
		array('label'=>'Rekap Bulanan','url'=>array('rekap')),
		array('label'=>'Ketidakhadiran','url'=>array('absen'),'active'=>true),
		array('label'=>'Perubahan Jam','url'=>array('jam')),
		array('label'=>'Hari Libur','url'=>array('libur')),
	)
));

?>

<?php 
echo $this->renderPartial('absen_form', array('model'=>$model));
if(isset($msg)) 
	echo '<script>alert("'.$msg.'")</script>';

echo CHtml::link(BHtml::icon('menu-left').' Kembali',array('absen','tahun'=>$tahun));
Yii::app()->clientScript->registerScript('js','
	$("#CalendarPersonal_PersonalCalendarStatus").change(function(){
		var text = $(this).find(":selected").text();
		if(text.indexOf(" ")>1)
			$("#CalendarPersonal_PersonalCalendarReason").val(text.slice(0,text.indexOf(" ")));
		else
			$("#CalendarPersonal_PersonalCalendarReason").val(text);
	});
');
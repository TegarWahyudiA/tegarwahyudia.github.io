<?php
$this->pageCaption='Update Kegiatan';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan'),
	$model->unitkerja->unitkerja=>array('/kegiatan/unitkerja','id'=>$model->id_unitkerja),
	'Update',
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
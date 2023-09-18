<?php
$this->pageCaption='Add Pegawai';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='Define a new pegawai';
$this->breadcrumbs=array(
	'Master Pegawai'=>array('pegawai'),
	'Add',
);
?>

<?php echo $this->renderPartial('pegawai_form', array('model'=>$model)); ?>
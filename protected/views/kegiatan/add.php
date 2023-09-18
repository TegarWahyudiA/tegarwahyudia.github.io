<?php
$this->pageCaption='Add Kegiatan';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='Define a new kegiatan';
$this->breadcrumbs=array(
	'Daftar Kegiatan'=>array('/kegiatan'),
	Yii::app()->user->unitkerja=>array('unitkerja','id'=>Yii::app()->user->id_unitkerja),
	'Add New',
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
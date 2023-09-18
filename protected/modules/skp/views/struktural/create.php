<?php
$this->pageCaption='Add Kegiatan';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='Define a new kegiatan';
$this->breadcrumbs=array(
	'SKP',
	Yii::app()->user->unitkerja=>array('index'),
	'Add New',
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
<?php
$this->pageCaption='Update Kegiatan';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'SKP',
	Yii::app()->user->unitkerja=>array('index'),
	'Update Kegiatan',
);
?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
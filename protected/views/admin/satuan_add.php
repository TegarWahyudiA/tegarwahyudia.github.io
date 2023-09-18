<?php
$this->pageCaption='Add Satuan';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='Define a new satuan';
$this->breadcrumbs=array(
	'Master Satuan'=>array('satuan'),
	'Create',
);

?>

<?php echo $this->renderPartial('satuan_form', array('model'=>$model)); ?>
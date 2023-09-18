<?php
$this->pageCaption='Update Master Satuan ';
$this->pageTitle=Yii::app()->name . ' - ' . $this->pageCaption;
$this->pageDescription='';
$this->breadcrumbs=array(
	'Master Satuan'=>array('satuan'),
	'Update',
);
?>

<?php echo $this->renderPartial('satuan_form', array('model'=>$model)); ?>